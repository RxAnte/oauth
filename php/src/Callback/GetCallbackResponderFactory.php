<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Callback;

use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\OauthLoginReturnCookieHandler;
use RxAnte\OAuth\Cookies\OauthPkceCodeCookieHandler;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\Cookies\OauthStateCookieHandler;
use RxAnte\OAuth\TokenRepository\TokenRepository;
use Throwable;

readonly class GetCallbackResponderFactory
{
    public function __construct(
        private AbstractProvider $provider,
        private TokenRepository $tokenRepository,
        private OauthStateCookieHandler $stateCookieHandler,
        private OauthPkceCodeCookieHandler $pkceCodeCookieHandler,
        private OauthLoginReturnCookieHandler $loginReturnCookieHandler,
        private OauthSessionTokenCookieHandler $sessionTokenCookieHandler,
        private GetCallbackRespondWithInvalidState $respondWithInvalidState,
        private GetCallbackResponder|null $customRespondWithInvalidState = null,
    ) {
    }

    public function create(
        QueryParams $params,
        ServerRequestInterface $request,
    ): GetCallbackResponder {
        try {
            return $this->createInternal($params, $request);
        } catch (Throwable) {
            if ($this->customRespondWithInvalidState !== null) {
                return $this->customRespondWithInvalidState;
            }

            return $this->respondWithInvalidState();
        }
    }

    private function respondWithInvalidState(): GetCallbackResponder
    {
        if ($this->customRespondWithInvalidState !== null) {
            return $this->customRespondWithInvalidState;
        }

        return $this->respondWithInvalidState;
    }

    private function createInternal(
        QueryParams $params,
        ServerRequestInterface $request,
    ): GetCallbackResponder {
        $pkceCode = $this->pkceCodeCookieHandler->getCookieFromRequest(
            $request,
        )->getValue();

        $state = $this->stateCookieHandler->getCookieFromRequest(
            $request,
        )->getValue();

        if (
            $params->state === '' ||
            $params->code === '' ||
            $pkceCode === null ||
            $pkceCode === '' ||
            $state === null ||
            $state === ''
        ) {
            return $this->respondWithInvalidState();
        }

        $this->provider->setPkceCode($pkceCode);

        $accessToken = $this->provider->getAccessToken(
            'authorization_code',
            ['code' => $params->code],
        );

        return new GetCallbackRespondWithAccessTokens(
            tokenRepository: $this->tokenRepository,
            accessToken: $accessToken,
            loginReturnCookieHandler: $this->loginReturnCookieHandler,
            sessionTokenCookieHandler: $this->sessionTokenCookieHandler,
        );
    }
}
