<?php

namespace MeetMatt\Colla\Mood\Presentation\Console;

use MeetMatt\Colla\Mood\Domain\Email\EmailRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Email\EmailSendingServiceInterface;
use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\Feedback;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Identity\RandomIdGeneratorInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailFeedbackLinksCommand extends Command
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var EmailRepositoryInterface */
    private $emailRepository;

    /** @var FeedbackRepositoryInterface */
    private $feedbackRepository;

    /** @var RandomIdGeneratorInterface */
    private $idGenerator;

    /** @var EmailSendingServiceInterface */
    private $emailSender;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        EmailRepositoryInterface $emailRepository,
        FeedbackRepositoryInterface $feedbackRepository,
        RandomIdGeneratorInterface $idGenerator,
        EmailSendingServiceInterface $emailSender
    ) {
        parent::__construct();

        $this->teamRepository     = $teamRepository;
        $this->emailRepository    = $emailRepository;
        $this->feedbackRepository = $feedbackRepository;
        $this->idGenerator        = $idGenerator;
        $this->emailSender        = $emailSender;
    }

    protected function configure()
    {
        $this->setName('feedback:links:email')
            ->setDescription('Generate and send feedback links to team emails')
            ->addArgument('team-id', InputArgument::REQUIRED, 'Team ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $teamId = $input->getArgument('team-id');
        try {
            $team = $this->teamRepository->get($teamId);
        } catch (NotFoundException $exception) {
            $output->writeln('Error: ' . $exception->getMessage());

            return 1;
        }

        $output->writeln('Sending emails to team: ' . $team->getName());

        $emails = $this->emailRepository->get($teamId);

        $today             = date('Y-m-d');
        $requiredFeedbacks = $emails->count();
        $existingFeedbacks = $this->feedbackRepository->findByDate($teamId, $today, $requiredFeedbacks);
        $missingFeedbacks  = $requiredFeedbacks - count($existingFeedbacks);

        if ($missingFeedbacks > 0) {
            $newFeedbacks = [];
            for ($i = 0; $i < $missingFeedbacks; $i++) {
                $newFeedbacks[] = new Feedback($this->idGenerator->generate(), $teamId, $today);
            }
            $this->feedbackRepository->createMany($newFeedbacks);
            $existingFeedbacks = array_merge($existingFeedbacks, $newFeedbacks);
        }

        $emailsSent = 0;
        $allEmails = $emails->getAll();

        $output->writeln('Sending ' . count($existingFeedbacks) . ' emails');

        foreach ($existingFeedbacks as $feedback) {
            $currentEmail = array_shift($allEmails);

            $output->write('Sending email to ' . $currentEmail . ': ');
            $emailSent = $this->emailSender->send(
                $currentEmail,
                'New feedback link for today!',
                'Dear member of <b>' . $team->getName() . "</b> team,<br>
Here's your personal feedback link for today:<br>
<a href='" . getenv('APPLICATION_BASE_URL') . '/feedback/' . $feedback->getId() . "'>Feedback link</a><br>
<br>
Best regards,
Mood Bot"
            );

            if ($emailSent) {
                $output->writeln('<info>OK</info>');
                $emailsSent++;
            } else {
                $output->writeln('<error>FAIL</error>');
            }
        }

        $output->writeln('Sent ' . $emailsSent . ' emails out of ' . count($allEmails));
    }

}