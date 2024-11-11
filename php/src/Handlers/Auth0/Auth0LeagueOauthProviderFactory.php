<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use League\OAuth2\Client\Provider\AbstractProvider;

readonly class Auth0LeagueOauthProviderFactory
{
    public function __construct(
        private Auth0LeagueOauthProviderConfig $config,
        private WellKnownRepository $wellKnownRepository,
    ) {
    }

    public function create(): Auth0LeagueOauthProvider
    {
        $wellKnown = $this->wellKnownRepository->get();

        return new Auth0LeagueOauthProvider([
            'clientId' => $this->config->clientId,
            'clientSecret' => $this->config->clientSecret,
            'redirectUri' => $this->config->createCallbackUrl(
                'auth/callback/auth0',
            ),
            'urlAuthorize' => $wellKnown->authorizationEndpoint,
            'urlAccessToken' => $wellKnown->tokenEndpoint,
            'urlResourceOwnerDetails' => '',
            'pkceMethod' => AbstractProvider::PKCE_METHOD_S256,
            'scopes' => $this->config->scopes,
            'scopeSeparator' => ' ',
            'audience' => $this->config->audience,
        ]);
    }
}
