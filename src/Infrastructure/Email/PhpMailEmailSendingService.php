<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Email;

use MeetMatt\Colla\Mood\Domain\Email\EmailSendingServiceInterface;

class PhpMailEmailSendingService implements EmailSendingServiceInterface
{
    public function send(string $recipient, string $subject, string $body): bool
    {
        $headers = 'From: Mood Colla <no-reply@colla.io>' . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";

        return mail($recipient, $subject, $body, $headers);
    }
}