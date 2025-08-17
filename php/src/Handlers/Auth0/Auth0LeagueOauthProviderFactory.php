<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use League\OAuth2\Client\Provider\AbstractProvider;
use RxAnte\OAuth\Callback\GetCallbackAction;
use RxAnte\OAuth\Routes\RoutesFactory;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class Auth0LeagueOauthProviderFactory
{
    public function __construct(
        private RoutesFactory $routesFactory,
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
                uri: $this->routesFactory->create()
                    ->pluckClassName(className: GetCallbackAction::class)
                    ->pattern,
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
