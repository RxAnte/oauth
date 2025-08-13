<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal;

use DateTimeInterface;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Token\Signature;
use Lcobucci\JWT\UnencryptedToken as JwtToken;

readonly class EmptyJwt implements JwtToken
{
    public function headers(): DataSet
    {
        return new DataSet([], '');
    }

    public function isPermittedFor(string $audience): bool
    {
        return false;
    }

    public function isIdentifiedBy(string $id): bool
    {
        return false;
    }

    public function isRelatedTo(string $subject): bool
    {
        return false;
    }

    public function hasBeenIssuedBy(string ...$issuers): bool
    {
        return false;
    }

    public function hasBeenIssuedBefore(DateTimeInterface $now): bool
    {
        return false;
    }

    public function isMinimumTimeBefore(DateTimeInterface $now): bool
    {
        return false;
    }

    public function isExpired(DateTimeInterface $now): bool
    {
        return true;
    }

    public function toString(): string
    {
        return 'noop';
    }

    public function claims(): DataSet
    {
        return new DataSet([], '');
    }

    public function signature(): Signature
    {
        return new Signature('noop', 'noop');
    }

    public function payload(): string
    {
        return 'noop';
    }
}
