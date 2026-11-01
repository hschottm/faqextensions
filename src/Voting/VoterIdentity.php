<?php

declare(strict_types=1);

namespace Hschottm\FaqExtensionsBundle\Voting;

final class VoterIdentity
{
    public const COOKIE = 'faqextensions_voter';

    public function __construct(private readonly string $secret)
    {
    }

    public function createCookie(): string
    {
        $token = bin2hex(random_bytes(32));
        return $token.'.'.hash_hmac('sha256', $token, $this->secret);
    }

    public function isValid(string $cookie): bool
    {
        if (!preg_match('/^([a-f0-9]{64})\.([a-f0-9]{64})$/D', $cookie, $parts)) {
            return false;
        }
        return hash_equals(hash_hmac('sha256', $parts[1], $this->secret), $parts[2]);
    }

    public function voteSignature(int $id, int $vote): string
    {
        return hash_hmac('sha256', 'faq-vote:'.$id.':'.$vote, $this->secret);
    }

    public function isVoteSignatureValid(int $id, int $vote, string $signature): bool
    {
        return hash_equals($this->voteSignature($id, $vote), $signature);
    }

    /** @return list<string> */
    public function keys(string $cookie, ?int $memberId): array
    {
        if (!$this->isValid($cookie)) {
            throw new \InvalidArgumentException('Invalid voter cookie.');
        }
        $keys = [hash_hmac('sha256', 'browser:'.$cookie, $this->secret)];
        if ($memberId !== null) {
            $keys[] = hash_hmac('sha256', 'member:'.$memberId, $this->secret);
        }
        return $keys;
    }
}
