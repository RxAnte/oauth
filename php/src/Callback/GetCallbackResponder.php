<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Callback;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface GetCallbackResponder
{
    public function respond(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface;
}
