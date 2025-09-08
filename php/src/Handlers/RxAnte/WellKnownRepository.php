<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;

use function array_key_exists;
use function assert;
use function implode;
use function is_string;
use function json_decode;
use function json_validate;
use function md5;

readonly class WellKnownRepository
{
    public function __construct(
        private RxAnteConfig $config,
        private ClientFactory $clientFactory,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function get(): WellKnown
    {
        $url = $this->config->wellKnownUrl;

        $cache = $this->cachePool->getItem(implode('_', [
            $this->config->wellKnownCacheKey,
            md5($url),
        ]));

        if ($cache->isHit()) {
            $wellKnown = $cache->get();
            assert($wellKnown instanceof WellKnown);

            return $wellKnown;
        }

        $response = $this->clientFactory->create()->get($url, [
            RequestOptions::HEADERS => ['Accept' => 'application/json'],
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode !==  200) {
            throw new RuntimeException(
                implode(' ', [
                    'Unable to get WellKnown from',
                    $url . '.',
                    'Well Known URL returned a non-200 status code.',
                ]),
                $statusCode,
            );
        }

        $body = (string) $response->getBody();

        if (! json_validate($body)) {
            throw new RuntimeException(
                implode(' ', [
                    'Unable to get WellKnown from',
                    $url . '.',
                    'Well Known response is not valid JSON.',
                ]),
            );
        }

        /**
         * @var array{
         *     issuer: string,
         *     authorization_endpoint: string,
         *     token_endpoint: string,
         *     userinfo_endpoint: string,
         * } $json
         */
        $json = (array) json_decode($body, true);

        if (
            ! array_key_exists('issuer', $json) ||
            ! is_string($json['issuer']) ||
            ! array_key_exists('authorization_endpoint', $json) ||
            ! is_string($json['authorization_endpoint']) ||
            ! array_key_exists('token_endpoint', $json) ||
            ! is_string($json['token_endpoint']) ||
            ! array_key_exists('userinfo_endpoint', $json) ||
            ! is_string($json['userinfo_endpoint'])
        ) {
            throw new RuntimeException(
                implode(' ', [
                    'Unable to get WellKnown from',
                    $url . '.',
                    'Well Known response JSON does not contain needed data.',
                ]),
            );
        }

        $wellKnown = new WellKnown(
            issuer: $json['issuer'],
            authorizationEndpoint: $json['authorization_endpoint'],
            tokenEndpoint: $json['token_endpoint'],
            userinfoEndpoint: $json['userinfo_endpoint'],
        );

        $this->cachePool->save(
            $cache->set($wellKnown)->expiresAfter(
                $this->config->wellKnownCacheExpiresAfter,
            ),
        );

        return $wellKnown;
    }
}
