<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Callback;

use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Cookies\OauthLoginReturnCookieHandler;
use RxAnte\OAuth\Cookies\OauthSessionTokenCookieHandler;
use RxAnte\OAuth\TokenRepository\TokenRepository;

readonly class GetCallbackRespondWithAccessTokens implements GetCallbackResponder
{
    public function __construct(
        private TokenRepository $tokenRepository,
        private AccessTokenInterface $accessToken,
        private OauthLoginReturnCookieHandler $loginReturnCookieHandler,
        private OauthSessionTokenCookieHandler $sessionTokenCookieHandler,
    ) {
    }

    public function respond(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        $sendTo = $this->loginReturnCookieHandler->getCookieFromRequest(
            $request,
        )->getValue() ?? '/';

        $id = $this->tokenRepository->createSessionIdWithAccessToken(
            $this->accessToken,
        );

        $response = $this->sessionTokenCookieHandler->setResponseCookie(
            $response,
            $id,
        );

        return $response->withStatus(302)
            ->withHeader('Location', $sendTo);
    }
}
