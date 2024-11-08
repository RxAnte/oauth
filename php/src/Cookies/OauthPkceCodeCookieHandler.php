<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

readonly class OauthPkceCodeCookieHandler extends AbstractLoginCookieHandler
{
    public function getCookieName(): string
    {
        return 'oauth-pkce-code';
    }
}
