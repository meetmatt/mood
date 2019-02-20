<?php

namespace MeetMatt\Colla\Mood\Domain\Feedback;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;

interface FeedbackRepositoryInterface
{
    /**
     * @param string $teamId
     * @param string $date
     * @param int    $limit
     *
     * @return Feedback[]
     */
    public function findAllByDate(string $teamId, string $date, int $limit): array;

    /**
     * @param string $teamId
     *
     * @return Feedback[]
     */
    public function findNonEmptyByTeam(string $teamId): array;

    /**
     * @param string $id
     * @param string $date
     *
     * @return Feedback
     *
     * @throws NotFoundException
     */
    public function getOneByDate(string $id, string $date): Feedback;

    /**
     * @param Feedback[] $feedbacks
     */
    public function createMany(array $feedbacks): void;

    public function update(Feedback $feedback): void;
}