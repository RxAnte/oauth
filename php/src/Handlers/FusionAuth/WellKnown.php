<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
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
