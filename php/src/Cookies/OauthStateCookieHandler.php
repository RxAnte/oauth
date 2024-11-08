<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

readonly class OauthStateCookieHandler extends AbstractLoginCookieHandler
{
    public function getCookieName(): string
    {
        return 'oauth-state';
    }
}
