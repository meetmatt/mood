<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Email;

use MeetMatt\Colla\Mood\Domain\Email\EmailSendingServiceInterface;

class PhpMailEmailSendingService implements EmailSendingServiceInterface
{
    public function send(string $recipient, string $subject, string $body): void
    {
        $headers = 'From: no-reply@mood.colla.io' . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        mail($recipient, $subject, $body, $headers);
    }
}