<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use RxAnte\OAuth\UserInfo\Jwt;

interface GetFusionAuthResponse
{
    public function get(Jwt $jwt): FusionAuthResponse;
}
