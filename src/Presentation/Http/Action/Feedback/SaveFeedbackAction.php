<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Action\Feedback;

use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Feedback\Feedback;
use MeetMatt\Colla\Mood\Domain\Feedback\FeedbackRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouterInterface;

class SaveFeedbackAction
{
    /** @var FeedbackRepositoryInterface */
    private $feedbackRepository;

    /** @var RouterInterface */
    private $router;

    public function __construct(FeedbackRepositoryInterface $feedbackRepository, RouterInterface $router)
    {
        $this->feedbackRepository = $feedbackRepository;
        $this->router             = $router;
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
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        $form = $request->getParsedBody();
        $rating = isset($form['rating']) ? (int)$form['rating'] : 0;
        $comment = $form['comment'] ?? '';

        if ($rating > 0 && $rating < 6) {

            $this->feedbackRepository->update(
                new Feedback(
                    $feedback->getId(),
                    $feedback->getTeamId(),
                    $feedback->getDate(),
                    $comment,
                    $rating
                )
            );
        }

        return $response->withStatus(303)
            ->withHeader('Location', $this->router->pathFor('feedback_form', ['id' => $id], ['thankyou' => 1]));
    }
}