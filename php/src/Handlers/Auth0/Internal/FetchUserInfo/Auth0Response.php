<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use function mb_strtolower;

readonly class Auth0Response
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
