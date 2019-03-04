<?php

namespace MeetMatt\Colla\Mood\Domain\Identity;

interface IdGeneratorInterface
{
    public function generate(): string;
}