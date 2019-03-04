<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Identity;

use MeetMatt\Colla\Mood\Domain\Identity\IdGeneratorInterface;
use Ramsey\Uuid\Uuid;

class UuidGenerator implements IdGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4();
    }
}