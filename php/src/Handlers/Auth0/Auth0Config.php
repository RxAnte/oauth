<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

readonly class Auth0Config
{
    public function __construct(
        public string $userInfoUrl,
    ) {
    }
}
