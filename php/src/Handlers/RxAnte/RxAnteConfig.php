<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use DateInterval;

use function array_map;
use function explode;
use function in_array;

readonly class RxAnteConfig
{
    public DateInterval $wellKnownCacheExpiresAfter;

    /** @param string[] $m2mAuthorizedSubjects */
    public function __construct(
        public string $wellKnownUrl,
        public string $wellKnownCacheKey = 'rxante_auth_well_known',
        DateInterval|null $wellKnownCacheExpiresAfter = null,
        public array $m2mAuthorizedSubjects = [],
    ) {
        /** Enforce array is string values */
        array_map(
            static fn (string $s) => $s,
            $m2mAuthorizedSubjects,
        );

        if ($wellKnownCacheExpiresAfter !== null) {
            $this->wellKnownCacheExpiresAfter = $wellKnownCacheExpiresAfter;

            return;
        }

        $this->wellKnownCacheExpiresAfter = new DateInterval('PT24H');
    }

    public function m2mSubjectIsAuthorized(string $subject): bool
    {
        $auth = in_array(
            $subject,
            $this->m2mAuthorizedSubjects,
            true,
        );

        if ($auth) {
            return true;
        }

        $subject = explode('@', $subject)[0];

        return in_array(
            $subject,
            $this->m2mAuthorizedSubjects,
            true,
        );
    }
}
