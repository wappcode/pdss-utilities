<?php

namespace PDSSUtilities;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class DoctrineUniqueIDStringGenerator extends AbstractIdGenerator
{
    public function generate(EntityManagerInterface $em,  $entity): mixed
    {
        $randomStr = $this->randomString(3);
        $prefix =  rand() . "__";
        return $randomStr . md5(uniqid($prefix, true));
    }

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
}
