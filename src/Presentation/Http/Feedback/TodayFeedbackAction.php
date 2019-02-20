<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Feedback;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\Feedback;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Identity\RandomIdGeneratorInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class TodayFeedbackAction
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var FeedbackRepositoryInterface */
    private $feedbackRepository;

    /** @var RandomIdGeneratorInterface */
    private $idGenerator;

    /** @var Twig */
    private $twig;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        FeedbackRepositoryInterface $feedbackRepository,
        RandomIdGeneratorInterface $idGenerator,
        Twig $twig
    ) {
        $this->teamRepository     = $teamRepository;
        $this->feedbackRepository = $feedbackRepository;
        $this->idGenerator        = $idGenerator;
        $this->twig               = $twig;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {
        $queryParams   = $request->getQueryParams();
        $requiredCount = $queryParams['count'] && $queryParams['count'] < 20 ? (int)$queryParams['count'] : 15;

        $teamId = $arguments['id'];

        try {
            $team = $this->teamRepository->get($teamId);
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        $today        = date('Y-m-d');
        $feedbacks    = $this->feedbackRepository->findAllByDate($teamId, $today, $requiredCount);
        $needToCreate = $requiredCount - count($feedbacks);
        if ($needToCreate > 0) {
            $newFeedbacks = [];
            for ($i = 0; $i < $needToCreate; $i++) {
                $newFeedbacks[] = new Feedback($this->idGenerator->generate(), $teamId, $today);
            }

            $this->feedbackRepository->createMany($newFeedbacks);
            $feedbacks = array_merge($feedbacks, $newFeedbacks);
        }

        return $this->twig->render(
            $response,
            'today.html.twig',
            [
                'team'      => $team,
                'feedbacks' => $feedbacks,
            ]
        );
    }
}