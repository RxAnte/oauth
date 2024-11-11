<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Callback;

readonly class QueryParams
{
    public function __construct(
        public string $code,
        public string $state,
    ) {
    }
}
