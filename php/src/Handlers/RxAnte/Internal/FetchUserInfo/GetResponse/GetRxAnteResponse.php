<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

use Lcobucci\JWT\UnencryptedToken as JwtToken;

interface GetRxAnteResponse
{
    public function get(JwtToken $jwt): RxAnteResponseWrapper;
}
