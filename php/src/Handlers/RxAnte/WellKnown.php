<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

readonly class WellKnown
{
    public function __construct(
        public string $issuer,
        public string $authorizationEndpoint,
        public string $tokenEndpoint,
        public string $userinfoEndpoint,
    ) {
    }
}
