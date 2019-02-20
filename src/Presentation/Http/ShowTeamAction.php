<?php

namespace MeetMatt\Colla\Mood\Presentation\Http;

use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class ShowTeamAction
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var Twig */
    private $twig;

    public function __construct(TeamRepositoryInterface $teamRepository, Twig $twig)
    {
        $this->teamRepository = $teamRepository;
        $this->twig           = $twig;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments
    ): ResponseInterface
    {
        $id = $arguments['id'];

        $team = $this->teamRepository->get($id);

        return $this->twig->render($response, 'team.html.twig', ['team' => $team]);
    }
}