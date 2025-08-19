<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use Psr\Http\Message\ServerRequestInterface;

class SendToLoginCreateAuthUrlEvent
{
    public function __construct(
        private readonly ServerRequestInterface $request,
        private Url $url,
    ) {
    }

    public function request(): ServerRequestInterface
    {
        return $this->request;
    }

    public function findCookieValue(string $cookieName): string|null
    {
        return $this->request()->getCookieParams()[$cookieName] ?? null;
    }

    public function findQueryParam(string $param): string|null
    {
        return $this->request()->getQueryParams()[$param] ?? null;
    }

    public function url(): Url
    {
        return $this->url;
    }

    public function setUrl(Url $url): void
    {
        $this->url = $url;
    }
}
