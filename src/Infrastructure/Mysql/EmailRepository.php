<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Mysql;

use MeetMatt\Colla\Mood\Domain\Email\EmailCollection;
use MeetMatt\Colla\Mood\Domain\Email\EmailRepositoryInterface;
use ParagonIE\EasyDB\EasyDB;

class EmailRepository implements EmailRepositoryInterface
{
    /** @var EasyDB */
    private $db;

    public function __construct(EasyDB $db)
    {
        $this->db = $db;
    }

    public function get(string $teamId): EmailCollection
    {
        $rows = $this->db->run(
            'SELECT `email` FROM `email` WHERE `team_id` = ? ORDER BY `email` ASC',
            $teamId
        );

        $emails = [];
        foreach ($rows as $row) {
            $emails[] = $row['email'];
        }

        return new EmailCollection($emails);
    }

    public function update(string $teamId, EmailCollection $emails): void
    {
        $this->db->delete('email', ['team_id' => $teamId]);

        $rows = [];
        foreach ($emails->getAll() as $email) {
            $rows[] = [
                'team_id' => $teamId,
                'email'   => $email,
            ];
        }

        if (!empty($rows)) {
            $this->db->insertMany('email', $rows);
        }
    }
}