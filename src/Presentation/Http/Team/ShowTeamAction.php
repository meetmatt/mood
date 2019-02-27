<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Team;

use MeetMatt\Colla\Mood\Domain\Email\EmailRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class ShowTeamAction
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var EmailRepositoryInterface */
    private $emailRepository;

    /** @var Twig */
    private $twig;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        EmailRepositoryInterface $emailRepository,
        Twig $twig
    ) {
        $this->teamRepository  = $teamRepository;
        $this->emailRepository = $emailRepository;
        $this->twig            = $twig;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface {

        $id = $arguments['id'];

        try {
            $team   = $this->teamRepository->get($id);
            $emails = $this->emailRepository->get($team->getId());
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        return $this->twig->render($response, 'team.html.twig', ['team' => $team, 'emails' => $emails]);
    }
}