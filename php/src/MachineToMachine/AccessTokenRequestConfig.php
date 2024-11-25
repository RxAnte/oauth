<?php

declare(strict_types=1);

namespace RxAnte\OAuth\MachineToMachine;

readonly class AccessTokenRequestConfig
{
    public function __construct(
        public string $tokenUrl,
        public string $clientId,
        public string $clientSecret,
        public string $audience,
        public string $grantType = 'client_credentials',
    ) {
    }

    /** @return string[] */
    public function requestOptionsArray(): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'audience' => $this->audience,
            'grant_type' => $this->grantType,
        ];
    }
}
