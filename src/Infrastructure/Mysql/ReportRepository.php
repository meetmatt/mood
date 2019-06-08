<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Mysql;

use DateTimeImmutable;
use MeetMatt\Colla\Mood\Domain\Feedback\DailyStatistics;
use MeetMatt\Colla\Mood\Domain\Feedback\DailyStatisticsCollection;
use MeetMatt\Colla\Mood\Domain\Feedback\DateRange;
use MeetMatt\Colla\Mood\Domain\Feedback\ReportRepositoryInterface;
use ParagonIE\EasyDB\EasyDB;

class ReportRepository implements ReportRepositoryInterface
{
    /** @var EasyDB */
    private $db;

    public function __construct(EasyDB $easyDb)
    {
        $this->db = $easyDb;
    }

    public function findDailyStatistics(string $teamId, DateRange $dateRange): DailyStatisticsCollection
    {
        $query = '
            SELECT
                AVG(rating) as average,
                `date`
            FROM
                feedback
            WHERE
                team_id = ?
                AND
                rating IS NOT NULL
                AND
                `date` BETWEEN ? AND ?
            GROUP BY
                `date`
        ';

        $rows = $this->db->run(
            $query,
            $teamId,
            $dateRange->getStart()->format('Y-m-d'),
            $dateRange->getEnd()->format('Y-m-d')
        );

        $statistics = new DailyStatisticsCollection();

        if ($rows) {
            foreach ($rows as $row) {
                $statistics->add($this->createFromRow($row));
            }
        }

        return $statistics;
    }

    private function createFromRow(array $row): DailyStatistics
    {
        return new DailyStatistics(
            DateTimeImmutable::createFromFormat('Y-m-d', $row['date']),
            $row['average']
        );
    }
}
