<?php

namespace MeetMatt\Colla\Mood\Domain\Team;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;

interface TeamRepositoryInterface
{
    public function create(string $name): string;

    /**
     * @param string $id
     *
     * @return Team
     *
     * @throws NotFoundException
     */
    public function get(string $id): Team;
}