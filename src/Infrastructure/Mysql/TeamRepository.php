<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Mysql;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Identity\IdGeneratorInterface;
use MeetMatt\Colla\Mood\Domain\Team\Team;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use ParagonIE\EasyDB\EasyDB;

class TeamRepository implements TeamRepositoryInterface
{
    /** @var EasyDB */
    private $db;

    /** @var IdGeneratorInterface */
    private $idGenerator;

    public function __construct(EasyDB $db, IdGeneratorInterface $idGenerator)
    {
        $this->db          = $db;
        $this->idGenerator = $idGenerator;
    }

    public function create(string $name): string
    {
        $id = $this->idGenerator->generate();

        $this->db->insert(
            'team',
            [
                'id'   => $id,
                'name' => $name,
            ]
        );

        return $id;
    }

    public function get(string $id): Team
    {
        $result = $this->db->row('SELECT * FROM team WHERE id = ?', $id);
        if (!$result) {
            throw new NotFoundException('Team not found: ' . $id);
        }

        return new Team($result['id'], $result['name']);
    }
}