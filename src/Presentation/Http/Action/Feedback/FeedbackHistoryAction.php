<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Action\Feedback;

use DateTimeImmutable;
use InvalidArgumentException;
use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\DateRange;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Feedback\ReportRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class FeedbackHistoryAction
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var FeedbackRepositoryInterface */
    private $feedbackRepository;

    /** @var ReportRepositoryInterface */
    private $reportRepository;

    /** @var Twig */
    private $twig;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        FeedbackRepositoryInterface $feedbackRepository,
        ReportRepositoryInterface $reportRepository,
        Twig $twig
    ) {
        $this->teamRepository     = $teamRepository;
        $this->feedbackRepository = $feedbackRepository;
        $this->reportRepository   = $reportRepository;
        $this->twig               = $twig;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface {
        $teamId = $arguments['id'];

        try {
            $team = $this->teamRepository->get($teamId);
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        $queryParams = $request->getQueryParams();

        if (!empty($queryParams['dateRange'])) {
            try {
                $dateRange = DateRange::createFromString($queryParams['dateRange']);
            } catch (InvalidArgumentException $exception) {
                // Use default values, if range format is invalid.
                $dateRange = DateRange::createDefault(new DateTimeImmutable());
            }
        } else {
            $dateRange = DateRange::createDefault(new DateTimeImmutable());
        }

        $feedbacks = $this->feedbackRepository->find($team->getId(), $dateRange);

        $statistics = $this->reportRepository->findDailyStatistics($team->getId(), $dateRange);

        return $this->twig->render(
            $response,
            'history.html.twig',
            [
                'team'       => $team,
                'feedbacks'  => $feedbacks,
                'statistics' => $statistics,
                'dateRange'  => $dateRange,
            ]
        );
    }
}
