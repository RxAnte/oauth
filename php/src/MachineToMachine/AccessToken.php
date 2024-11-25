<?php

declare(strict_types=1);

namespace RxAnte\OAuth\MachineToMachine;

readonly class AccessToken
{
    public function __construct(
        public string $accessToken,
        public string $scope,
        public int $expiresIn,
        public string $tokenType,
    ) {
    }
}
