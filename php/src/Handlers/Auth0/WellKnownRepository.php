<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;

use function array_key_exists;
use function assert;
use function json_decode;
use function json_validate;

readonly class WellKnownRepository
{
    public function __construct(
        private Auth0Config $config,
        private ClientFactory $clientFactory,
        private CacheItemPoolInterface $cachePool,
    ) {
    }

    public function get(): WellKnown
    {
        $url = $this->config->wellKnownUrl;

        $cache = $this->cachePool->getItem(
            $this->config->wellKnownCacheKey,
        );

        if ($cache->isHit()) {
            $wellKnown = $cache->get();
            assert($wellKnown instanceof WellKnown);

            return $wellKnown;
        }

        $response = $this->clientFactory->create()->get(
            $url,
            [
                RequestOptions::HEADERS => ['Accept' => 'application/json'],
                RequestOptions::HTTP_ERRORS => false,
            ],
        );

        $statusCode = $response->getStatusCode();

        if ($statusCode !==  200) {
            throw new RuntimeException(
                'Unable to get Auth0 Well Known from ' . $url,
            );
        }

        $body = (string) $response->getBody();

        if (! json_validate($body)) {
            throw new RuntimeException(
                'Unable to get Auth0 Well Known from ' . $url,
            );
        }

        $json = (array) json_decode($body, true);
        assert(array_key_exists('issuer', $json));
        assert(array_key_exists('authorization_endpoint', $json));
        assert(array_key_exists('token_endpoint', $json));
        assert(array_key_exists('userinfo_endpoint', $json));

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
