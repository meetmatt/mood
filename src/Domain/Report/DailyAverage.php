<?php

namespace MeetMatt\Colla\Mood\Domain\Report;

final class DailyAverage
{
    /** @var float */
    private $averageRating;

    /** @var string */
    private $date;

    public function __construct(float $averageRating, string $date)
    {
        $this->averageRating = $averageRating;
        $this->date          = $date;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
