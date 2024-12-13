<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GenericProvider;
use RxAnte\OAuth\Callback\GetCallbackAction;
use RxAnte\OAuth\Routes\RoutesFactory;

readonly class FusionAuthLeagueOauthProviderFactory
{
    public function __construct(
        private RoutesFactory $routesFactory,
        private FusionAuthConfig $fusionAuthConfig,
        private WellKnownRepository $wellKnownRepository,
        private FusionAuthLeagueOauthProviderConfig $config,
    ) {
    }

    public function create(): GenericProvider
    {
        $wellKnown = $this->wellKnownRepository->get();

        return new GenericProvider(
            [
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
            ],
            [
                'httpClient' => new HttpClient([
                    RequestOptions::VERIFY => $this->fusionAuthConfig->sslVerify,
                ]),
            ],
        );
    }
}
