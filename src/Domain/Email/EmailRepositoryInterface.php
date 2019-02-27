<?php

namespace MeetMatt\Colla\Mood\Domain\Email;

interface EmailRepositoryInterface
{
    public function get(string $teamId): EmailCollection;

    public function update(string $teamId, EmailCollection $emails): void;
}