<?php

namespace MeetMatt\Colla\Mood\Domain\Feedback;

use DateTimeInterface;

final class DailyStatistics
{
    /** @var DateTimeInterface */
    private $date;

    /** @var float */
    private $averageRating;

    public function __construct(DateTimeInterface $date, float $averageRating)
    {
        $this->date          = $date;
        $this->averageRating = $averageRating;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getAverageRating(): float
    {
        return $this->averageRating;
    }
}
