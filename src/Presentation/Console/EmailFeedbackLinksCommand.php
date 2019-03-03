<?php

namespace MeetMatt\Colla\Mood\Presentation\Console;


use MeetMatt\Colla\Mood\Domain\Email\TeamMailerService;
use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmailFeedbackLinksCommand extends Command
{
    /** @var TeamMailerService */
    private $teamMailerService;

    public function __construct(TeamMailerService $teamMailerService)
    {
        parent::__construct();

        $this->teamMailerService = $teamMailerService;
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
            $emailsSent = $this->teamMailerService->sendFeedbackLinks($teamId);
        } catch (NotFoundException $exception) {
            $output->writeln('Error: ' . $exception->getMessage());

            return 1;
        }

        $output->writeln('Sent ' . $emailsSent . ' emails');

        return 0;
    }

}