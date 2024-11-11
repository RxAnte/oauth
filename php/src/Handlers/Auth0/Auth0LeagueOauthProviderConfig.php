<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use function implode;
use function ltrim;
use function rtrim;

readonly class Auth0LeagueOauthProviderConfig
{
    /** @param string[] $scopes */
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $callbackDomain,
        public string $audience,
        public array $scopes = [
            'openid',
            'profile',
            'email',
            'offline_access',
        ],
    ) {
    }

    public function createCallbackUrl(string $uri): string
    {
        return implode('/', [
            rtrim($this->callbackDomain, '/'),
            ltrim($uri, '/'),
        ]);
    }
}
