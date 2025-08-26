<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Request;

use function array_merge;
use function count;
use function json_encode;

readonly class Payload
{
    /** @param array<array-key, mixed> $payload */
    public function __construct(public array $payload = [])
    {
    }

    public function withItem(string|int $key, mixed $item): Payload
    {
        $payload = $this->payload;

        $payload[$key] = $item;

        return new self($payload);
    }

    public function withoutItem(string|int $key): Payload
    {
        $payload = $this->payload;

        if (isset($payload[$key])) {
            unset($payload[$key]);
        }

        return new self($payload);
    }

    /** @param array<array-key, mixed> $payload */
    public function withPayload(array $payload): Payload
    {
        return new self($payload);
    }

    /** @param array<array-key, mixed> $payload */
    public function withAddedPayload(array $payload): Payload
    {
        $params = array_merge(
            $this->payload,
            $payload,
        );

        return new self($payload);
    }

    public function prepareForRequest(): string|null
    {
        if (count($this->payload) < 1) {
            return null;
        }

        return (string) json_encode($this->payload);
    }
}
