<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth;

use function implode;
use function ltrim;
use function rtrim;

readonly class FusionAuthLeagueOauthProviderConfig
{
    /** @param string[] $scopes */
    public function __construct(
        public string $clientId,
        public string $clientSecret,
        public string $callbackDomain,
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
