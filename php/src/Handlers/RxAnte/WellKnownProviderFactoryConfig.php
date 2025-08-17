<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use function array_map;
use function array_values;

readonly class WellKnownProviderFactoryConfig
{
    /** @var string[] */
    public array $scopes;

    /** @param string[] $scopes */
    public function __construct(
        public string $appBaseUrl,
        public string $clientId,
        public string $clientSecret,
        array $scopes = [
            'openid',
            'profile',
            'email',
            'offline_access',
        ],
    ) {
        $this->scopes = array_values(array_map(
            static fn (string $scope) => $scope,
            $scopes,
        ));
    }
}
