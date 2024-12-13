<?php

/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth;

use DateInterval;
use OpenSSLAsymmetricKey;
use OpenSSLCertificate;

use function explode;
use function in_array;

readonly class FusionAuthConfig
{
    public DateInterval $wellKnownCacheExpiresAfter;

    /**
     * @param string|resource|OpenSSLAsymmetricKey|OpenSSLCertificate $signingCertificate
     * @param string[]                                                $m2mAuthorizedSubjects
     */
    public function __construct(
        public string $wellKnownUrl,
        public mixed $signingCertificate,
        public bool $sslVerify = true,
        public string $signingCertificateAlgorithm = 'RS256',
        public string $wellKnownCacheKey = 'fusion_auth_well_known',
        DateInterval|null $wellKnownCacheExpiresAfter = null,
        public array $m2mAuthorizedSubjects = [],
    ) {
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