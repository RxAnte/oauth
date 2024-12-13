<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use function mb_strtolower;

readonly class FusionAuthResponse
{
    public function __construct(
        public int $statusCode,
        public string $body,
    ) {
    }

    public function isValid(): bool
    {
        return $this->statusCode === 200
            && mb_strtolower($this->body) !== 'unauthorized';
    }

    public function isNotValid(): bool
    {
        return ! $this->isValid();
    }
}
