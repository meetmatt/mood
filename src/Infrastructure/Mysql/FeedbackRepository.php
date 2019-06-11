<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Mysql;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\DateRange;
use MeetMatt\Colla\Mood\Domain\Feedback\Feedback;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use ParagonIE\EasyDB\EasyDB;

class FeedbackRepository implements FeedbackRepositoryInterface
{
    /** @var EasyDB */
    private $db;

    public function __construct(EasyDB $easyDb)
    {
        $this->db = $easyDb;
    }

    public function findByDate(string $teamId, string $date, int $limit): array
    {
        $feedback = [];

        $rows = $this->db->run(
            'SELECT * FROM feedback WHERE team_id = ? AND `date` = ? LIMIT ?',
            $teamId,
            $date,
            $limit
        );
        if ($rows) {
            foreach ($rows as $row) {
                $feedback[] = $this->createFromRow($row);
            }
        }

        return $feedback;
    }

    public function find(string $teamId, DateRange $dateRange): array
    {
        $query = '
            SELECT 
                * 
            FROM 
                feedback 
            WHERE 
                team_id = ? 
                AND 
                rating IS NOT NULL
                AND
                `date` BETWEEN ? AND ?
            ORDER BY 
                `date` DESC
        ';

        $rows = $this->db->run(
            $query,
            $teamId,
            $dateRange->getStart()->format('Y-m-d'),
            $dateRange->getEnd()->format('Y-m-d')
        );

        $feedback = [];

        if ($rows) {
            foreach ($rows as $row) {
                $feedback[] = $this->createFromRow($row);
            }
        }

        return $feedback;
    }

    public function getOneByDate(string $id, string $date): Feedback
    {
        $row = $this->db->row('SELECT * FROM feedback WHERE id = ? AND `date` = ?', $id, $date);
        if (!$row) {
            throw new NotFoundException('Feedback ' . $id . ' not found');
        }

        return $this->createFromRow($row);
    }

    /**
     * @param Feedback[] $feedbacks
     */
    public function createMany(array $feedbacks): void
    {
        $map = [];
        foreach ($feedbacks as $feedback) {
            $map[] = $this->mapToArray($feedback);
        }

        $this->db->insertMany('feedback', $map);
    }

    public function update(Feedback $feedback): void
    {
        $this->db->update('feedback', $this->mapToArray($feedback), ['id' => $feedback->getId()]);
    }

    private function createFromRow(array $row): Feedback
    {
        return new Feedback(
            $row['id'],
            $row['team_id'],
            $row['date'],
            $row['comment'],
            $row['rating']
        );
    }

    private function mapToArray(Feedback $feedback): array
    {
        return [
            'id'      => $feedback->getId(),
            'team_id' => $feedback->getTeamId(),
            'date'    => $feedback->getDate(),
            'comment' => $feedback->getComment(),
            'rating'  => $feedback->getRating(),
        ];
    }
}
