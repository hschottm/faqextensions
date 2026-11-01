<?php

declare(strict_types=1);

namespace Hschottm\FaqExtensionsBundle\Voting;

use Doctrine\DBAL\Connection;

final class VoteRecorder
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function findFaq(int $id): array|false
    {
        return $this->connection->fetchAssociative('SELECT id, pid, question FROM tl_faq WHERE id=? AND published=1 AND showhelpful=1', [$id]);
    }

    /** @param list<string> $keys */
    public function hasVoted(int $id, array $keys): bool
    {
        foreach ($keys as $key) {
            if ($this->connection->fetchOne('SELECT id FROM tl_faq_vote WHERE pid=? AND voter=?', [$id, $key]) !== false) {
                return true;
            }
        }
        return false;
    }

    /** @param non-empty-list<string> $keys */
    public function record(int $id, int $vote, array $keys): string
    {
        if (!in_array($vote, [-1, 1], true) || !$keys) {
            throw new \InvalidArgumentException('A vote and voter identity are required.');
        }

        return $this->connection->transactional(function () use ($id, $vote, $keys): string {
            // Serialize votes for this FAQ before reading claims. SQLite locks on
            // this write as well, allowing the same transaction to be tested there.
            $this->connection->executeStatement('UPDATE tl_faq SET id=id WHERE id=?', [$id]);
            if (!$this->findFaq($id)) {
                return 'unavailable';
            }
            $duplicate = $this->hasVoted($id, $keys);
            foreach (array_unique($keys) as $key) {
                if (!$this->hasVoted($id, [$key])) {
                    // Also link a newly logged-in member to an earlier browser vote.
                    $this->connection->insert('tl_faq_vote', ['pid' => $id, 'voter' => $key, 'tstamp' => time()]);
                }
            }
            if ($duplicate) {
                return 'duplicate';
            }
            $field = $vote === 1 ? 'helpful' : 'nothelpful';
            $this->connection->executeStatement("UPDATE tl_faq SET $field=$field+1 WHERE id=?", [$id]);
            return 'accepted';
        });
    }
}
