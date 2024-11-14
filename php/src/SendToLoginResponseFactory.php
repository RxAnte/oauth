<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider as OauthClientProvider;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\SendToLogInCookieChain;

readonly class SendToLoginResponseFactory
{
    public function __construct(
        private OauthClientProvider $provider,
        private ResponseFactoryInterface $responseFactory,
        private SendToLogInCookieChain $sendToLogInCookieChain,
    ) {
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * It is important to make this call first, otherwise `getPkceCode` and
         * `getState` won't work correctly (not a "stateless" service,
         * unfortunately)
         */
        $authorizationUrl = $this->provider->getAuthorizationUrl();

        $response = $this->sendToLogInCookieChain->set(
            request: $request,
            response: $this->responseFactory->createResponse(),
            oauthPkceCode: (string) $this->provider->getPkceCode(),
            oauthState: $this->provider->getState(),
        );

        return $response->withStatus(302)
            ->withHeader('Location', $authorizationUrl);
    }
}
