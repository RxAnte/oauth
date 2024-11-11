<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

use DateTimeImmutable;

readonly class OauthSessionTokenCookieHandler extends AbstractLoginCookieHandler
{
    public function getCookieName(): string
    {
        return 'oauth-session-token';
    }

    public function getCookieExpiration(): DateTimeImmutable|null
    {
        return null;
    }
}
