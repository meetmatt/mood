<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Action\Team;

use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouterInterface;

class CreateTeamAction
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var RouterInterface */
    private $router;

    public function __construct(TeamRepositoryInterface $teamRepository, RouterInterface $router)
    {
        $this->teamRepository = $teamRepository;
        $this->router         = $router;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        $id = $this->teamRepository->create($parsedBody['name']);

        $teamPageUrl = $this->router->pathFor('show_team', ['id' => $id]);

        return $response->withStatus(303)->withHeader('Location', $teamPageUrl);
    }
}