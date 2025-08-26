<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

use DateTimeImmutable;

readonly class OauthSessionTokenCookieHandler extends AbstractLoginCookieHandler
{
    public const COOKIE_NAME = 'oauth-session-token';

    public function getCookieName(): string
    {
        return self::COOKIE_NAME;
    }

    public function getCookieExpiration(): DateTimeImmutable|null
    {
        return null;
    }
}
