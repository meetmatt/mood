<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Email;

use MeetMatt\Colla\Mood\Domain\Email\EmailSendingServiceInterface;

class NoopEmailSendingService implements EmailSendingServiceInterface
{
    public function send(string $recipient, string $subject, string $body): void
    {
        echo 'Sending email to ' . $recipient . ': [' . $subject . '] ' . $body . PHP_EOL;
    }
}