<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\EmptyJwt;

readonly class FetchUserInfoFactory
{
    public function __construct(
        private FetchUserInfoNoOp $noOp,
        private FetchUserInfoFromRxAnteAuth $fromRxAnteAuth,
    ) {
    }

    public function create(JwtToken $jwt): FetchUserInfo
    {
        if ($jwt instanceof EmptyJwt) {
            return $this->noOp;
        }

        // TODO: Implement M2M

        return $this->fromRxAnteAuth;
    }
}
