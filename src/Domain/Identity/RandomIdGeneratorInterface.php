<?php

namespace MeetMatt\Colla\Mood\Domain\Identity;

interface RandomIdGeneratorInterface
{
    public function generate(): string;
}