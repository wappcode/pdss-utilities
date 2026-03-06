<?php

namespace PDSSUtilities;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class DoctrineKsuidGenerator extends AbstractIdGenerator
{
    // KSUID epoch (2014-05-13T16:53:20Z)
    private const EPOCH_STAMP = 1400000000;

    public function generateId(EntityManagerInterface $em, object|null $entity): mixed
    {
        return $this->generateKsuid();
    }

    protected function generateKsuid(): string
    {
        // KSUID: 20 bytes total
        // Structure: 4 bytes timestamp + 16 bytes random payload

        // Get current timestamp (seconds since KSUID epoch)
        $timestamp = time() - self::EPOCH_STAMP;

        // Timestamp as 4 bytes (32 bits)
        $timestampBytes = pack('N', $timestamp);

        // Random payload (16 bytes / 128 bits)
        $payload = random_bytes(16);

        // Combine timestamp + payload
        $ksuidBytes = $timestampBytes . $payload;

        // Encode to base62
        return $this->base62Encode($ksuidBytes);
    }

    protected function base62Encode(string $data): string
    {
        $base62 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Convert bytes to a large integer
        $num = gmp_import($data, 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);

        if (gmp_cmp($num, '0') === 0) {
            return '0';
        }

        $encoded = '';
        $base = gmp_init(62);

        while (gmp_cmp($num, '0') > 0) {
            list($num, $remainder) = gmp_div_qr($num, $base);
            $encoded = $base62[gmp_intval($remainder)] . $encoded;
        }

        // KSUID should be exactly 27 characters, pad with zeros if needed
        return str_pad($encoded, 27, '0', STR_PAD_LEFT);
    }
}
