<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository\Refresh;

use League\OAuth2\Client\Token\AccessTokenInterface;

interface GetRefreshedAccessToken
{
    public function get(
        AccessTokenInterface $accessToken,
    ): AccessTokenInterface|null;
}
