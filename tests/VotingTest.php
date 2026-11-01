<?php

declare(strict_types=1);

namespace Hschottm\FaqExtensionsBundle\Tests;

use Doctrine\DBAL\DriverManager;
use Hschottm\FaqExtensionsBundle\Voting\VoteRecorder;
use Hschottm\FaqExtensionsBundle\Voting\VoterIdentity;
use PHPUnit\Framework\TestCase;

final class VotingTest extends TestCase
{
    private function database(?string $path = null): \Doctrine\DBAL\Connection
    {
        $db = DriverManager::getConnection($path ? ['driver' => 'pdo_sqlite', 'path' => $path] : ['driver' => 'pdo_sqlite', 'memory' => true]);
        $db->executeStatement('PRAGMA busy_timeout=10000');
        return $db;
    }

    private function schema(\Doctrine\DBAL\Connection $db): void
    {
        $db->executeStatement('CREATE TABLE tl_faq (id INTEGER PRIMARY KEY, pid INTEGER, question TEXT, published INTEGER, showhelpful INTEGER, helpful INTEGER, nothelpful INTEGER)');
        $db->executeStatement('CREATE TABLE tl_faq_vote (id INTEGER PRIMARY KEY, pid INTEGER, voter TEXT, tstamp INTEGER, UNIQUE(pid,voter))');
        foreach ([1, 2] as $id) {
            $db->insert('tl_faq', ['id' => $id, 'pid' => 1, 'question' => 'Question', 'published' => 1, 'showhelpful' => 1, 'helpful' => 10, 'nothelpful' => 3]);
        }
    }

    public function testSignedCookieAndMemberIdentity(): void
    {
        $identity = new VoterIdentity('test-secret');
        $first = $identity->createCookie();
        $second = $identity->createCookie();
        self::assertTrue($identity->isValid($first));
        self::assertTrue($identity->isVoteSignatureValid(1, 1, $identity->voteSignature(1, 1)));
        self::assertFalse($identity->isVoteSignatureValid(2, 1, $identity->voteSignature(1, 1)));
        self::assertFalse($identity->isVoteSignatureValid(1, -1, $identity->voteSignature(1, 1)));
        self::assertFalse($identity->isValid('invalid'));
        self::assertFalse((new VoterIdentity('another-secret'))->isValid($first));
        self::assertNotSame($identity->keys($first, null), $identity->keys($second, null));
        self::assertSame($identity->keys($first, 7)[1], $identity->keys($second, 7)[1]);
        self::assertNotSame($identity->keys($first, 7)[1], $identity->keys($first, 8)[1]);
    }

    public function testRepeatedVotesAcrossBrowsersAndLoginCountOnce(): void
    {
        $db = $this->database();
        $this->schema($db);
        $recorder = new VoteRecorder($db);
        $identity = new VoterIdentity('test-secret');
        $browser = $identity->createCookie();
        self::assertSame('accepted', $recorder->record(1, 1, $identity->keys($browser, null)));
        self::assertSame('duplicate', $recorder->record(1, -1, $identity->keys($browser, null)));
        self::assertSame('duplicate', $recorder->record(1, 1, $identity->keys($browser, 7)));
        $other = $identity->createCookie();
        self::assertSame('duplicate', $recorder->record(1, -1, $identity->keys($other, 7)));
        // Logging out in the second browser must not create another vote either.
        self::assertSame('duplicate', $recorder->record(1, -1, $identity->keys($other, null)));
        self::assertSame('accepted', $recorder->record(2, -1, $identity->keys($browser, 7)));
        self::assertSame('accepted', $recorder->record(1, -1, $identity->keys($identity->createCookie(), 8)));
        self::assertSame(['helpful' => 11, 'nothelpful' => 4], $db->fetchAssociative('SELECT helpful, nothelpful FROM tl_faq WHERE id=1'));
        self::assertSame(4, (int) $db->fetchOne('SELECT nothelpful FROM tl_faq WHERE id=2'));
    }

    public function testControllerUsesTheAuthenticatedMemberAcrossBrowsers(): void
    {
        $db = $this->database();
        $this->schema($db);
        $identity = new VoterIdentity('test-secret');
        $csrf = $this->createMock(\Contao\CoreBundle\Csrf\ContaoCsrfTokenManager::class);
        $csrf->method('isTokenValid')->willReturn(true);
        $user = $this->createMock(\Contao\FrontendUser::class);
        $user->method('__get')->with('id')->willReturn(7);
        $tokens = new \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage();
        $tokens->setToken(new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, 'contao_frontend', ['ROLE_MEMBER']));
        $twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader(['@HschottmFaqExtensions/vote.html.twig' => '{{ status }}']));
        $controller = new \Hschottm\FaqExtensionsBundle\Controller\VoteController(new VoteRecorder($db), $identity, $csrf, 'test-token', $tokens, $twig);
        foreach (['accepted', 'duplicate'] as $expected) {
            $request = \Symfony\Component\HttpFoundation\Request::create('/vote?signature='.$identity->voteSignature(1, 1), 'POST', ['REQUEST_TOKEN' => 'test'], [VoterIdentity::COOKIE => $identity->createCookie()]);
            self::assertSame($expected, $controller($request, 1, 1)->getContent());
        }
        self::assertSame(11, (int) $db->fetchOne('SELECT helpful FROM tl_faq WHERE id=1'));
    }

    public function testDisabledAndUnpublishedFaqsCannotReceiveVotes(): void
    {
        $db = $this->database();
        $this->schema($db);
        $db->executeStatement('UPDATE tl_faq SET published=0 WHERE id=1');
        $db->executeStatement('UPDATE tl_faq SET showhelpful=0 WHERE id=2');
        $recorder = new VoteRecorder($db);
        foreach ([1, 2, 999] as $id) {
            self::assertSame('unavailable', $recorder->record($id, 1, ['voter']));
        }
        self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM tl_faq_vote'));
    }

    public function testFailedCounterUpdateRollsBackIdentityClaims(): void
    {
        $db = $this->database();
        $this->schema($db);
        $db->executeStatement("CREATE TRIGGER reject_vote BEFORE UPDATE OF helpful ON tl_faq BEGIN SELECT RAISE(ABORT, 'test failure'); END");
        try {
            (new VoteRecorder($db))->record(1, 1, ['browser', 'member']);
            self::fail('Expected a failed counter update.');
        } catch (\Doctrine\DBAL\Exception $exception) {
            self::assertSame(0, (int) $db->fetchOne('SELECT COUNT(*) FROM tl_faq_vote'));
            self::assertSame(10, (int) $db->fetchOne('SELECT helpful FROM tl_faq WHERE id=1'));
        }
    }

    public function testConcurrentVotesAreSerialized(): void
    {
        if (!function_exists('pcntl_fork')) {
            self::markTestSkipped('pcntl is needed for concurrent database connections.');
        }
        $path = tempnam(sys_get_temp_dir(), 'faq-votes-');
        $db = $this->database($path);
        $this->schema($db);
        $db->close();
        try {
            $children = [];
            for ($i = 0; $i < 6; ++$i) {
                $pid = pcntl_fork();
                if ($pid === -1) { throw new \RuntimeException('Could not fork test worker.'); }
                if ($pid === 0) {
                    try {
                        $connection = $this->database($path);
                        $recorder = new VoteRecorder($connection);
                        $recorder->record(1, 1, ['same-browser', 'same-member']);
                        $recorder->record(2, -1, ['visitor-'.$i]);
                        $connection->close();
                        exit(0);
                    } catch (\Throwable $e) {
                        fwrite(STDERR, $e->getMessage());
                        exit(1);
                    }
                }
                $children[] = $pid;
            }
            foreach ($children as $pid) {
                pcntl_waitpid($pid, $status);
                self::assertSame(0, pcntl_wexitstatus($status));
            }
            $db = $this->database($path);
            self::assertSame(11, (int) $db->fetchOne('SELECT helpful FROM tl_faq WHERE id=1'));
            self::assertSame(9, (int) $db->fetchOne('SELECT nothelpful FROM tl_faq WHERE id=2'));
            self::assertSame(2, (int) $db->fetchOne('SELECT COUNT(*) FROM tl_faq_vote WHERE pid=1'));
        } finally {
            $db->close();
            unlink($path);
        }
    }
}
