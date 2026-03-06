<?php

namespace PDSSUtilities;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class DoctrineUlidGenerator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, object|null $entity): mixed
    {
        return $this->generateUlid();
    }

    protected function generateUlid(): string
    {
        // ULID: 26 characters (Crockford's Base32)
        // Structure: 10 chars timestamp + 16 chars randomness

        // Timestamp part (48 bits = 10 chars in base32)
        $timestamp = (int)(microtime(true) * 1000);

        // Random part (80 bits = 16 chars in base32)
        $randomBytes = random_bytes(10);

        // Crockford's Base32 encoding
        $encoding = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

        // Encode timestamp (10 characters)
        $timestampChars = '';
        for ($i = 9; $i >= 0; $i--) {
            $mod = $timestamp % 32;
            $timestampChars = $encoding[$mod] . $timestampChars;
            $timestamp = (int)($timestamp / 32);
        }

        // Encode random part (16 characters)
        $randomValue = 0;
        foreach (str_split($randomBytes) as $byte) {
            $randomValue = ($randomValue << 8) | ord($byte);
        }

        $randomChars = '';
        for ($i = 0; $i < 16; $i++) {
            $randomChars = $encoding[$randomValue & 31] . $randomChars;
            $randomValue >>= 5;
        }

        return $timestampChars . $randomChars;
    }
}
