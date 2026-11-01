<?php

namespace Hschottm\FaqExtensionsBundle\Tests;

use Contao\DC_Table;
use Contao\Input;
use Contao\System;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

final class FaqCopyTest extends TestCase
{
    public function testCopyResetsVotesAndPreservesSourceAndSettings(): void
    {
        $container = new ContainerBuilder();
        $session = new Session(new MockArraySessionStorage());
        $bag = new AttributeBag();
        $bag->setName('contao_backend');
        $session->registerBag($bag);
        $request = Request::create('/contao');
        $request->setSession($session);
        $stack = new RequestStack();
        $stack->push($request);
        $container->set('request_stack', $stack);
        System::setContainer($container);
        $GLOBALS['TL_DCA']['tl_faq'] = ['palettes' => ['default' => '{publish_legend},published']];
        require __DIR__.'/../src/Resources/contao/dca/tl_faq.php';
        $source = ['helpful' => 12, 'nothelpful' => 4, 'showhelpful' => '1', 'recommended' => '1', 'viewcount' => 42];

        try {
            foreach (['copy', 'copyAll'] as $action) {
                Input::setGet('act', $action);
                $driver = new CopyProbe($source);
                try {
                    $driver->copy(true);
                    self::fail('Expected the copy preparation to reach positioning.');
                } catch (CopyPrepared $prepared) {
                    self::assertSame(0, $prepared->record['helpful']);
                    self::assertSame(0, $prepared->record['nothelpful']);
                    self::assertSame('1', $prepared->record['showhelpful']);
                    self::assertSame('1', $prepared->record['recommended']);
                    self::assertSame(42, $prepared->record['viewcount']);
                    self::assertSame($source, $driver->getCurrentRecord());
                }
            }
        } finally {
            Input::setGet('act', null);
            System::setContainer(new ContainerBuilder());
            unset($GLOBALS['TL_DCA']['tl_faq']);
        }
    }
}

// Run Contao's real copy field preparation; stop before positioning/database writes.
class CopyProbe extends DC_Table
{
    public function __construct(private array $source)
    {
        $this->strTable = 'tl_faq';
        $this->intId = 1;
    }

    public function getCurrentRecord(int|string|null $id = null, string|null $table = null): ?array
    {
        return $this->source;
    }

    protected function getNewPosition($mode, $pid = null, $insertInto = false)
    {
        throw new CopyPrepared($this->set);
    }
}

class CopyPrepared extends \RuntimeException
{
    public function __construct(public array $record)
    {
        parent::__construct('Copy field preparation completed.');
    }
}
