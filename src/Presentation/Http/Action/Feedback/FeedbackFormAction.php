<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Action\Feedback;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class FeedbackFormAction
{
    /** @var FeedbackRepositoryInterface */
    private $feedbackRepository;

    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var Twig */
    private $twig;

    public function __construct(
        FeedbackRepositoryInterface $feedbackRepository,
        TeamRepositoryInterface $teamRepository,
        Twig $twig
    ) {
        $this->feedbackRepository = $feedbackRepository;
        $this->teamRepository     = $teamRepository;
        $this->twig               = $twig;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface
    {
        $id   = $arguments['id'];
        $date = date('Y-m-d');

        try {
            $feedback = $this->feedbackRepository->getOneByDate($id, $date);
            $team     = $this->teamRepository->get($feedback->getTeamId());
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        $query = $request->getQueryParams();

        return $this->twig->render(
            $response,
            'form.html.twig',
            [
                'team'     => $team,
                'feedback' => $feedback,
                'thankyou' => array_key_exists('thankyou', $query),
            ]
        );
    }
}