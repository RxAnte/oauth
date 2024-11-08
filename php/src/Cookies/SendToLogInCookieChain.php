<?php

/** @noinspection PhpUnnecessaryLocalVariableInspection */

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class SendToLogInCookieChain
{
    public function __construct(
        private OauthLoginReturnCookieHandler $loginReturn,
        private OauthPkceCodeCookieHandler $pkceCode,
        private OauthStateCookieHandler $state,
    ) {
    }

    public function set(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $oauthPkceCode,
        string $oauthState,
    ): ResponseInterface {
        $loginReturn = $request->getUri()->getPath();

        if ($request->getUri()->getQuery() !== '') {
            $loginReturn .= '?' . $request->getUri()->getQuery();
        }

        $response = $this->loginReturn->setResponseCookie(
            $response,
            $loginReturn,
        );

        $response = $this->pkceCode->setResponseCookie(
            $response,
            $oauthPkceCode,
        );

        $response = $this->state->setResponseCookie(
            $response,
            $oauthState,
        );

        return $response;
    }
}
