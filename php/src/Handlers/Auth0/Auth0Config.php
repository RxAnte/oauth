<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use DateInterval;

readonly class Auth0Config
{
    public DateInterval $wellKnownCacheExpiresAfter;

    public function __construct(
        public string $userInfoUrl,
        public string $wellKnownUrl,
        public string $wellKnownCacheKey = 'auth_0_well_known',
        DateInterval|null $wellKnownCacheExpiresAfter = null,
    ) {
        if ($wellKnownCacheExpiresAfter !== null) {
            $this->wellKnownCacheExpiresAfter = $wellKnownCacheExpiresAfter;

            return;
        }

        $this->wellKnownCacheExpiresAfter = new DateInterval('PT24H');
    }
}
