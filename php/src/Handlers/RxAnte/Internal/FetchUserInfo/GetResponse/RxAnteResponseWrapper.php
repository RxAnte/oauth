<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

readonly class RxAnteResponseWrapper
{
    public function __construct(
        public RxAnteResponse $response,
        public bool $isFromCache = false,
    ) {
    }
}
