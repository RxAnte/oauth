<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

use DateInterval;
use DateTimeImmutable;
use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract readonly class AbstractLoginCookieHandler implements CookieHandlerInterface
{
    public function __construct(private ClockInterface $clock)
    {
    }

    public function getCookieExpiration(): DateTimeImmutable|null
    {
        return $this->clock->now()->add(
            new DateInterval('P5M'),
        );
    }

    public function getCookieFromRequest(RequestInterface $request): Cookie
    {
        return FigRequestCookies::get(
            $request,
            $this->getCookieName(),
        );
    }

    public function setResponseCookie(
        ResponseInterface $response,
        string $value,
    ): ResponseInterface {
        return FigResponseCookies::set(
            $response,
            SetCookie::create($this->getCookieName())
                ->withValue($value)
                ->withPath('/')
                ->withExpires($this->getCookieExpiration()),
        );
    }
}
