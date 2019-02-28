<?php

namespace MeetMatt\Colla\Mood\Domain\Email;

interface EmailSendingServiceInterface
{
    public function send(string $recipient, string $subject, string $body): bool;
}