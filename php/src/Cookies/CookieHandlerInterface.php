<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

use DateTimeImmutable;
use Dflydev\FigCookies\Cookie;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface CookieHandlerInterface
{
    public function getCookieName(): string;

    public function getCookieExpiration(): DateTimeImmutable;

    public function getCookieFromRequest(RequestInterface $request): Cookie;

    public function setResponseCookie(
        ResponseInterface $response,
        string $value,
    ): ResponseInterface;
}
