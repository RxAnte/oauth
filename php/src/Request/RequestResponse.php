<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Request;

use Psr\Http\Message\ResponseInterface;

use function json_decode;
use function json_validate;

readonly class RequestResponse
{
    public function __construct(private ResponseInterface $response)
    {
    }

    /**
     * @return string[][] Returns an associative array of the message's headers.
     *                    Each key is the header name, and each value is an
     *                    array of strings for that header.
     */
    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->response->hasHeader($name);
    }

    /**
     * @return string[] An array of string values as provided for the given
     *                  header or an empty array
     */
    public function getHeader(string $name): array
    {
        return $this->response->getHeader($name);
    }

    /**
     * @return string A string of values as provided for the given header
     *                concatenated together using a comma. If the header does
     *                not appear in the message an empty string is returned.
     */
    public function getHeaderLine(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function getBody(): string
    {
        return $this->response->getBody()->__toString();
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }

    public function isOk(): bool
    {
        $code = $this->getStatusCode();

        return $code > 199 && $code < 300;
    }

    public function is1xx(): bool
    {
        $code = $this->getStatusCode();

        return $code > 99 && $code < 200;
    }

    public function is2xx(): bool
    {
        return $this->isOk();
    }

    public function is3xx(): bool
    {
        $code = $this->getStatusCode();

        return $code > 299 && $code < 400;
    }

    public function is4xx(): bool
    {
        $code = $this->getStatusCode();

        return $code > 399 && $code < 500;
    }

    public function is5xx(): bool
    {
        $code = $this->getStatusCode();

        return $code > 499 && $code < 600;
    }

    /** @return mixed[] */
    public function getJson(): array
    {
        $body = $this->getBody();

        if (! json_validate($body)) {
            $msg = 'The response body is not valid JSON';

            return [
                'error' => 'invalid_json',
                'error_description' => $msg,
                'message' => $msg,
            ];
        }

        /** @phpstan-ignore-next-line */
        return json_decode($body, true);
    }
}
