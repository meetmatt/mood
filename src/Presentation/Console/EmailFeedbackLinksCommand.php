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

        $output->writeln('Require ' . $requiredFeedbacks . ' feedbacks to be generated');
        $output->writeln('Found ' . count($existingFeedbacks) . ' generated feedbacks');

        $missingFeedbacks  = $requiredFeedbacks - count($existingFeedbacks);

        if ($missingFeedbacks > 0) {

            $output->writeln('Generating ' . $missingFeedbacks . ' feedbacks');

            $newFeedbacks = [];
            for ($i = 0; $i < $missingFeedbacks; $i++) {
                $newFeedbacks[] = new Feedback($this->idGenerator->generate(), $teamId, $today);
            }
            $this->feedbackRepository->createMany($newFeedbacks);
            $existingFeedbacks = array_merge($existingFeedbacks, $newFeedbacks);
        }

        $emailsSent = 0;
        $allEmails = $emails->getAll();
        $totalEmails = count($allEmails);

        $output->writeln('Sending ' . count($existingFeedbacks) . ' emails');

        foreach ($existingFeedbacks as $feedback) {
            $currentEmail = array_shift($allEmails);

            $output->write('Sending email to ' . $currentEmail . ': ');
            $emailSent = $this->emailSender->send(
                $currentEmail,
                'New feedback link for today!',
                '<html lang="en"><body>Dear member of <b>' . $team->getName() . "</b> team,
<br>
<br>
Here's your personal feedback link for today: <a href='" . getenv('APPLICATION_BASE_URL') . '/feedback/' . $feedback->getId() . "'>Feedback link</a><br>
<br>
<br>
Feel free to contact the app maintainer on <a href='https://github.com/meetmatt/mood'>GitHub</a>.<br>
Contributions welcome!
<br><br>
Here's some additional text to fool the apache sp am filters which require 3200 bytes of human text in the email.<br>
If you are aware how else to improve the chance of email getting into inbox instead of junk, please contact the maintainer.<br>
<br>
The quick, brown fox jumps over a lazy dog. DJs flock by when MTV ax quiz prog.<br>
Junk MTV quiz graced by fox whelps. Bawds jog, flick quartz, vex nymphs.<br>
Waltz, bad nymph, for quick jigs vex! Fox nymphs grab quick-jived waltz.<br>
Brick quiz whangs jumpy veldt fox. Bright vixens jump; dozy fowl quack.<br>
Quick wafting zephyrs vex bold Jim. Quick zephyrs blow, vexing daft Jim.<br>
Top-charged fop blew my junk TV quiz. How quickly daft jumping zebras vex.<br>
Two driven jocks help fax my big quiz. Quick, Baz, get my woven flax jodhpurs!<br>
\"Now fax quiz Jack!\" my brave ghost pled.<br>

<br><br>
Best regards,
<br>
Mood Bot</body></html>"
            );

            if ($emailSent) {
                $output->writeln('<info>OK</info>');
                $emailsSent++;
            } else {
                $output->writeln('<error>FAIL</error>');
            }
        }

        $output->writeln('Sent ' . $emailsSent . ' emails out of ' . $totalEmails);
    }

}