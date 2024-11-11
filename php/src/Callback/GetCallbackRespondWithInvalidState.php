<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Callback;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class GetCallbackRespondWithInvalidState implements GetCallbackResponder
{
    public function respond(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $response = $response->withStatus(400);

        $response->getBody()->write('Invalid state');

        return $response;
    }
}
