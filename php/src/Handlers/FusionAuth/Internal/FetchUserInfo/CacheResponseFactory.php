<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

readonly class CacheResponseFactory
{
    public function __construct(
        private CacheResponseNoOp $noOp,
        private CacheResponseWithSystemCache $withSystemCache,
    ) {
    }

    public function create(FusionAuthResponse $response): CacheResponse
    {
        if ($response->isNotValid()) {
            return $this->noOp;
        }

        return $this->withSystemCache;
    }
}
