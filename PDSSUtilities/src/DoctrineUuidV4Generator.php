<?php

declare(strict_types=1);

namespace PDSSUtilities;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class DoctrineUuidV4Generator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, object|null $entity): mixed
    {
        return $this->generateUuidV4();
    }

    protected function generateUuidV4(): string
    {
        $data = random_bytes(16);

        // Set version to 0100 (UUID v4)
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

        // Set variant to 10xx (RFC 4122)
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
