<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
interface GetAuth0Response
{
    public function get(Jwt $jwt): Auth0Response;
}
