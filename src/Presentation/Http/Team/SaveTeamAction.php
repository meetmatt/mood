<?php

namespace MeetMatt\Colla\Mood\Presentation\Http\Team;

use MeetMatt\Colla\Mood\Domain\Email\EmailCollection;
use MeetMatt\Colla\Mood\Domain\Email\EmailRepositoryInterface;
use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use MeetMatt\Colla\Mood\Domain\Team\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouterInterface;

class SaveTeamAction
{
    /** @var TeamRepositoryInterface */
    private $teamRepository;

    /** @var EmailRepositoryInterface */
    private $emailRepository;

    /** @var RouterInterface */
    private $router;

    public function __construct(
        TeamRepositoryInterface $teamRepository,
        EmailRepositoryInterface $emailRepository,
        RouterInterface $router
    ) {
        $this->teamRepository  = $teamRepository;
        $this->emailRepository = $emailRepository;
        $this->router          = $router;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $arguments = []
    ): ResponseInterface {
        $id = $arguments['id'];

        try {
            $team = $this->teamRepository->get($id);
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        $parsedBody  = $request->getParsedBody();
        if (is_array($parsedBody['emails'])) {
            $inputEmails = array_filter($parsedBody['emails']);
        } else {
            $inputEmails = array_filter(explode("\r\n", $parsedBody['emails']));
        }

        $emails = [];
        foreach ($inputEmails as $email) {
            $email = trim($email);

            $validatedEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
            if ($validatedEmail === $email) {
                $emails[] = $email;
            }
        }

        $this->emailRepository->update($team->getId(), new EmailCollection($emails));

        $teamPageUrl = $this->router->pathFor('show_team', ['id' => $id]);

        return $response->withStatus(303)->withHeader('Location', $teamPageUrl);
    }
}