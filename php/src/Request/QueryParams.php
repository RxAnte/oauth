<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Request;

use function array_keys;
use function array_map;
use function array_merge;
use function count;
use function http_build_query;

readonly class QueryParams
{
    /** @param array<string, string> $params */
    public function __construct(public array $params = [])
    {
        array_map(
            static fn (string $key) => $key,
            array_keys($params),
        );

        array_map(
            static fn (string $val) => $val,
            $params,
        );
    }

    public function withParam(string $key, string $val): QueryParams
    {
        $params = $this->params;

        $params[$key] = $val;

        return new self($params);
    }

    public function withoutQueryParam(string $key): QueryParams
    {
        $params = $this->params;

        if (isset($params[$key])) {
            unset($params[$key]);
        }

        return new self($params);
    }

    /** @param array<string, string> $params */
    public function withParams(array $params): QueryParams
    {
        return new self($params);
    }

    /** @param array<string, string> $params */
    public function withAddedParams(array $params): QueryParams
    {
        $params = array_merge(
            $this->params,
            $params,
        );

        return new self($params);
    }

    public function asQueryString(): string
    {
        if (count($this->params) < 1) {
            return '';
        }

        return '?' . http_build_query($this->params);
    }
}
