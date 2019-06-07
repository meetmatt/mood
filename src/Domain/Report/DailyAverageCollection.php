<?php

namespace MeetMatt\Colla\Mood\Domain\Report;

class DailyAverageCollection
{
    private $averages;

    public function add(DailyAverage $average): void
    {
        $this->averages[] = $average;
    }

    /**
     * @return DailyAverage[]
     */
    public function all(): array
    {
        return $this->averages;
    }
}
