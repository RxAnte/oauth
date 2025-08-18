<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use League\OAuth2\Client\Provider\AbstractProvider;
use RxAnte\OAuth\Callback\GetCallbackAction;
use RxAnte\OAuth\Routes\RoutesFactory;

readonly class WellKnownProviderFactory
{
    public function __construct(
        private RoutesFactory $routesFactory,
        private WellKnownProviderFactoryConfig $config,
        private WellKnownRepository $wellKnownRepository,
    ) {
    }

    public function create(): AbstractProvider
    {
        $wellKnown = $this->wellKnownRepository->get();

        $options = [
            'clientId' => $this->config->clientId,
            'clientSecret' => $this->config->clientSecret,
            'redirectUri' => $this->config->appBaseUrl . $this->routesFactory->create()
                    ->pluckClassName(className: GetCallbackAction::class)
                    ->pattern,
            'urlAuthorize' => $wellKnown->authorizationEndpoint,
            'urlAccessToken' => $wellKnown->tokenEndpoint,
            'urlResourceOwnerDetails' => '',
            'pkceMethod' => AbstractProvider::PKCE_METHOD_S256,
            'scopes' => $this->config->scopes,
            'scopeSeparator' => ' ',
        ];

        if ($this->config->audience !== null) {
            $options['audience'] = $this->config->audience;
        }

        return new RxAnteOauthProvider($options);
    }
}
