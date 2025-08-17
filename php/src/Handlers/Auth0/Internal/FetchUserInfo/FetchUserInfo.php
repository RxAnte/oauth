<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
interface FetchUserInfo
{
    public function fetch(Jwt $jwt): OauthUserInfo;
}
