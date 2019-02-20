<?php

namespace MeetMatt\Colla\Mood\Domain\Feedback;

final class Feedback
{
    /** @var string */
    private $id;

    /** @var string */
    private $teamId;

    /** @var string */
    private $date;

    /** @var string */
    private $comment;

    /** @var int|null */
    private $rating;

    public function __construct(string $id, string $teamId, string $date, string $comment = '', ?int $rating = null)
    {
        $this->id      = $id;
        $this->teamId  = $teamId;
        $this->date    = $date;
        $this->comment = $comment;
        $this->rating  = $rating;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTeamId(): string
    {
        return $this->teamId;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }
}