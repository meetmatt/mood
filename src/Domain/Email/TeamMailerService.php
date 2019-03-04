<?php

namespace MeetMatt\Colla\Mood\Domain\Email;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\Feedback;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Identity\IdGeneratorInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Slim\Views\Twig;

class TeamMailerService
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var EmailRepositoryInterface */
    private $emailRepository;

    /** @var FeedbackRepositoryInterface */
    private $feedbackRepository;

    /** @var IdGeneratorInterface */
    private $idGenerator;

    /** @var EmailSendingServiceInterface */
    private $emailSender;

    /** @var Twig */
    private $twig;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        EmailRepositoryInterface $emailRepository,
        FeedbackRepositoryInterface $feedbackRepository,
        IdGeneratorInterface $idGenerator,
        EmailSendingServiceInterface $emailSender,
        Twig $twig
    )
    {
        $this->teamRepository     = $teamRepository;
        $this->emailRepository    = $emailRepository;
        $this->feedbackRepository = $feedbackRepository;
        $this->idGenerator        = $idGenerator;
        $this->emailSender        = $emailSender;
        $this->twig               = $twig;
    }

    /**
     * @param string $teamId
     *
     * @throws NotFoundException
     *
     * @return int
     */
    public function sendFeedbackLinks(string $teamId): int
    {
        $team = $this->teamRepository->get($teamId);

        $emails = $this->emailRepository->get($teamId);

        $today             = date('Y-m-d');
        $requiredFeedbacks = $emails->count();
        $existingFeedbacks = $this->feedbackRepository->findByDate($teamId, $today, $requiredFeedbacks);

        $missingFeedbacks = $requiredFeedbacks - count($existingFeedbacks);

        if ($missingFeedbacks > 0) {
            $newFeedbacks = [];
            for ($i = 0; $i < $missingFeedbacks; $i++) {
                $newFeedbacks[] = new Feedback($this->idGenerator->generate(), $teamId, $today);
            }
            $this->feedbackRepository->createMany($newFeedbacks);
            $existingFeedbacks = array_merge($existingFeedbacks, $newFeedbacks);
        }

        $emailsSent = 0;
        $allEmails  = $emails->getAll();

        foreach ($existingFeedbacks as $feedback) {
            $currentEmail = array_shift($allEmails);

            $emailSent = $this->emailSender->send(
                $currentEmail,
                'New feedback link for today!',
                $this->twig->fetch('email.html.twig', ['team' => $team, 'feedback' => $feedback])
            );

            if ($emailSent) {
                $emailsSent++;
            }
        }

        return $emailsSent;
    }
}