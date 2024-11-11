<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository;

use League\OAuth2\Client\Token\AccessTokenInterface;
use ReturnTypeWillChange;

readonly class EmptyAccessToken implements AccessTokenInterface
{
    public function getToken(): string
    {
        return '';
    }

    public function getRefreshToken(): string|null
    {
        return null;
    }

    public function getExpires(): null
    {
        return null;
    }

    public function hasExpired(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    public function getValues(): array
    {
        return [];
    }

    /** @inheritDoc */
    public function __toString()
    {
        return '';
    }

    /**
     * @inheritDoc
     * @phpstan-ignore-next-line
     */
    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [];
    }
}
