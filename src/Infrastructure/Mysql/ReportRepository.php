<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Mysql;

use MeetMatt\Colla\Mood\Domain\Report\DailyAverage;
use MeetMatt\Colla\Mood\Domain\Report\DailyAverageCollection;
use MeetMatt\Colla\Mood\Domain\Report\ReportRepositoryInterface;
use ParagonIE\EasyDB\EasyDB;

class ReportRepository implements ReportRepositoryInterface
{
    /** @var EasyDB */
    private $db;

    public function __construct(EasyDB $easyDb)
    {
        $this->db = $easyDb;
    }

    public function findDailyStatistics(string $teamId): DailyAverageCollection
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
            GROUP BY
                `date`
        ';

        $rows = $this->db->run($query, $teamId);

        $statistics = new DailyAverageCollection();

        if ($rows) {
            foreach ($rows as $row) {
                $statistics->add($this->createFromRow($row));
            }
        }

        return $statistics;
    }

    private function createFromRow(array $row): DailyAverage
    {
        return new DailyAverage($row['average'], $row['date']);
    }
}
