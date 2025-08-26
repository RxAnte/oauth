<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Request;

use GuzzleHttp\Psr7\Request;
use Hyperf\Guzzle\ClientFactory;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\TokenRepository\TokenRepository;

readonly class RequestApi
{
    /** @var array<string, string> */
    private array $cookieGlobals;

    /** @param array<string, string>|null $cookieGlobals */
    public function __construct(
        private RequestApiConfig $config,
        private AbstractProvider $provider,
        private ClientFactory $clientFactory,
        private TokenRepository $tokenRepository,
        private OauthSessionTokenCookieHandler $sessionTokenCookieHandler,
        array|null $cookieGlobals = null,
    ) {
        if ($cookieGlobals !== null) {
            $this->cookieGlobals = $cookieGlobals;

            return;
        }

        $this->cookieGlobals = $_COOKIE;
    }

    public function makeWithoutToken(
        RequestProperties $properties = new RequestProperties(),
    ): RequestResponse {
        $request = new Request(
            $properties->method->name,
            $this->config->createUrl(
                $properties->uri,
                $properties->queryParams,
            ),
            [
                'Accept' => ['application/json'],
                'Content-Type' => ['application/json'],
            ],
            $properties->payload->prepareForRequest(),
        );

        return new RequestResponse(
            $this->clientFactory->create()->sendRequest(
                $request,
            ),
        );
    }

    public function makeWithToken(
        AccessTokenInterface $token,
        RequestProperties $properties = new RequestProperties(),
    ): RequestResponse {
        $request = $this->provider->getAuthenticatedRequest(
            $properties->method->name,
            $this->config->createUrl(
                $properties->uri,
                $properties->queryParams,
            ),
            $token,
            [
                'headers' => [
                    'Accept' => ['application/json'],
                    'Content-Type' => ['application/json'],
                ],
                'body' => $properties->payload->prepareForRequest(),
            ],
        );

        return new RequestResponse(
            $this->clientFactory->create()->sendRequest(
                $request,
            ),
        );
    }

    public function makeWithTokenFromRequestCookies(
        ServerRequestInterface $serverRequest,
        RequestProperties $properties = new RequestProperties(),
    ): RequestResponse {
        $cookie = $this->sessionTokenCookieHandler->getCookieFromRequest(
            $serverRequest,
        );

        $token = $this->tokenRepository->getTokenBySessionId(
            $cookie->getValue() ?? 'noop',
        );

        return $this->makeWithToken($token, $properties);
    }

    public function makeWithTokenFromCookieGlobals(
        RequestProperties $properties = new RequestProperties(),
    ): RequestResponse {
        $cookieName = $this->sessionTokenCookieHandler->getCookieName();

        $cookieValue = $this->cookieGlobals[$cookieName] ?? 'noop';

        $token = $this->tokenRepository->getTokenBySessionId(
            $cookieValue,
        );

        return $this->makeWithToken($token, $properties);
    }
}
