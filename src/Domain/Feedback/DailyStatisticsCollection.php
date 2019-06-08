<?php

namespace MeetMatt\Colla\Mood\Domain\Feedback;

class DailyStatisticsCollection
{
    /** @var DailyStatistics[] */
    private $statistics;

    public function __construct(array $statistics = [])
    {
        $this->statistics = $statistics;
    }

    public function add(DailyStatistics $statistics): void
    {
        $this->statistics[] = $statistics;
    }

    /**
     * @return DailyStatistics[]
     */
    public function all(): array
    {
        return $this->statistics;
    }

    public function getWholeAverage(): float
    {
        $statisticsCount = count($this->statistics);

        if ($statisticsCount === 0)
        {
            return 0;
        }

        $sum = 0;
        foreach ($this->statistics as $statistics) {
            $sum += $statistics->getAverageRating();
        }

        return round($sum / $statisticsCount, 1);
    }

    public function getBestDay(): ?DailyStatistics
    {
        return array_reduce(
            $this->statistics,
            static function (?DailyStatistics $a, DailyStatistics $b) {
                if ($a === null) {
                    return $b;
                }

                return $a->getAverageRating() > $b->getAverageRating() ? $a : $b;
            }
        );
    }

    public function getWorstDay(): ?DailyStatistics
    {
        return array_reduce(
            $this->statistics,
            static function (?DailyStatistics $a, DailyStatistics $b) {
                if ($a === null) {
                    return $b;
                }

                return $a->getAverageRating() < $b->getAverageRating() ? $a : $b;
            }
        );
    }
}
