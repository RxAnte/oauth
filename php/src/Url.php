<?php

declare(strict_types=1);

namespace RxAnte\OAuth;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

use function assert;

class Url extends Uri implements UriInterface
{
    public function toString(): string
    {
        return $this->__toString();
    }

    public function withQueryParam(string $key, string|null $value = null): Url
    {
        $uri = self::withQueryValue($this, $key, $value);
        assert($uri instanceof Url);

        return $uri;
    }

    public function withOnlyQueryParam(
        string $key,
        string|null $value = null,
    ): Url {
        $uri = $this->withQuery('');
        assert($uri instanceof Url);

        return $uri->withQueryParam($key, $value);
    }

    public function withoutQueryParam(string $key): Url
    {
        $uri = self::withoutQueryValue($this, $key);
        assert($uri instanceof Url);

        return $uri;
    }

    /** @param (string|null)[] $keyValueArray Associative array of key and values */
    public function withQueryParams(array $keyValueArray): Url
    {
        $uri = self::withQueryValues($this, $keyValueArray);
        assert($uri instanceof Url);

        return $uri;
    }

    /** @param (string|null)[] $keyValueArray Associative array of key and values */
    public function withOnlyQueryParams(array $keyValueArray): Url
    {
        $uri = $this->withQuery('');
        assert($uri instanceof Url);

        return $uri->withQueryParams($keyValueArray);
    }
}
