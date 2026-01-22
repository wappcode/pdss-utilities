<?php

namespace PDSSUtilities;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class DoctrineUniqueIDStringGenerator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, object|null $entity): mixed
    {
        return $this->uuidv4();
    }

    /**
     *
     * @param integer $howmany
     * @return void
     * @deprecated  version 5.0.0 Use uuidv4() instead.
     */
    protected function randomString($howmany = 6)
    {
        $letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',];
        $str = '';

        for ($i = 0; $i < $howmany; $i++) {
            $index = random_int(1, count($letters));
            $str .= $letters[--$index];
        }
        return $str;
    }
    protected  function uuidv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // set version to 0100
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
