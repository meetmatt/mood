<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Cryptography;

use MeetMatt\Colla\Mood\Domain\Identity\RandomIdGeneratorInterface;
use Ramsey\Uuid\Uuid;

class RandomIdGenerator implements RandomIdGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4();
    }
}