<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte;

use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;

use function assert;
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

        $cache = $this->cachePool->getItem(
            $this->config->wellKnownCacheKey . '_' . md5($url),
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
                'Unable to get RxAnte Well Known from ' . $url,
            );
        }

        $body = (string) $response->getBody();

        if (! json_validate($body)) {
            throw new RuntimeException(
                'Unable to get Auth0 Well Known from ' . $url,
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
