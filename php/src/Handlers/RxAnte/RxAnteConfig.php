<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use DateInterval;

readonly class RxAnteConfig
{
    public DateInterval $wellKnownCacheExpiresAfter;

    public function __construct(
        public string $wellKnownUrl,
        public string $wellKnownCacheKey = 'rxante_auth_well_known',
        DateInterval|null $wellKnownCacheExpiresAfter = null,
    ) {
        if ($wellKnownCacheExpiresAfter !== null) {
            $this->wellKnownCacheExpiresAfter = $wellKnownCacheExpiresAfter;

            return;
        }

        $this->wellKnownCacheExpiresAfter = new DateInterval('PT24H');
    }
}
