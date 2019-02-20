<?php

namespace MeetMatt\Colla\Mood\Presentation\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouterInterface;

class FindTeamAction
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getParsedBody()['id'];

        $teamPageUrl = $this->router->pathFor('show_team', ['id' => $id]);

        return $response->withStatus(303)->withHeader('Location', $teamPageUrl);
    }
}