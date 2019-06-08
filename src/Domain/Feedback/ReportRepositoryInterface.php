<?php

namespace MeetMatt\Colla\Mood\Domain\Feedback;

interface ReportRepositoryInterface
{
    public function findDailyStatistics(string $teamId, DateRange $dateRange): DailyStatisticsCollection;
}
