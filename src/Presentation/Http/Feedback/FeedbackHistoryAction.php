<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Feedback;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
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

    /** @var Twig */
    private $twig;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        FeedbackRepositoryInterface $feedbackRepository,
        Twig $twig
    ) {
        $this->teamRepository     = $teamRepository;
        $this->feedbackRepository = $feedbackRepository;
        $this->twig               = $twig;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface
    {
        $teamId = $arguments['id'];

        try {
            $team = $this->teamRepository->get($teamId);
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        $feedbacks = $this->feedbackRepository->find($teamId);

        return $this->twig->render(
            $response,
            'history.html.twig',
            [
                'team'      => $team,
                'feedbacks' => $feedbacks,
            ]
        );
    }
}