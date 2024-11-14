<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class CustomAuthenticationResult
{
    public function __construct(
        public ServerRequestInterface|null $request = null,
        public ResponseInterface|null $response = null,
    ) {
    }
}
