<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Cookies;

readonly class OauthLoginReturnCookieHandler extends AbstractLoginCookieHandler
{
    public function getCookieName(): string
    {
        return 'oauth-login-return';
    }
}
