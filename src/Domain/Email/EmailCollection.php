<?php

namespace MeetMatt\Colla\Mood\Domain\Email;

class EmailCollection
{
    /** @var string[] */
    private $emails;

    public function __construct(array $emails = [])
    {
        sort($emails);
        $this->emails = $emails;
    }

    public function add(string $email): void
    {
        $emails = $this->emails;

        $emails[] = $email;

        $this->emails = $emails;
    }

    /**
     * @return string[]
     */
    public function getAll(): array
    {
        return array_values($this->emails);
    }
}