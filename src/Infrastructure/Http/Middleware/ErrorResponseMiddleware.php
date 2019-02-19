<?php

namespace MeetMatt\Colla\Mood\Infrastructure\Http\Middleware;

use InvalidArgumentException;
use JsonException;
use MeetMatt\Colla\Mood\Domain\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ErrorResponseMiddleware
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface
    {
        try {
            $response = $next($request, $response);
        } catch (InvalidArgumentException $exception) {
            return $response->withStatus(400, $exception->getMessage());
        } catch (JsonException $exception) {
            return $response->withStatus(400, $exception->getMessage());
        } catch (NotFoundException $exception) {
            return $response->withStatus(404, $exception->getMessage());
        }

        return $response;
    }
}