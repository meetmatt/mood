<?php

namespace MeetMatt\Colla\Mood\Domain\Report;

interface ReportRepositoryInterface
{
    public function findDailyStatistics(string $teamId): DailyAverageCollection;
}
