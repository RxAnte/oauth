<?php

/** @noinspection PhpUnnecessaryLocalVariableInspection */

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class SendToLogInCookieChain
{
    public function __construct(
        private OauthLoginReturnCookieHandler $loginReturnCookieHandler,
        private OauthPkceCodeCookieHandler $pkceCodeCookieHandler,
        private OauthStateCookieHandler $stateCookieHandler,
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

        $response = $this->loginReturnCookieHandler->setResponseCookie(
            $response,
            $loginReturn,
        );

        $response = $this->pkceCodeCookieHandler->setResponseCookie(
            $response,
            $oauthPkceCode,
        );

        $response = $this->stateCookieHandler->setResponseCookie(
            $response,
            $oauthState,
        );

        return $response;
    }
}
