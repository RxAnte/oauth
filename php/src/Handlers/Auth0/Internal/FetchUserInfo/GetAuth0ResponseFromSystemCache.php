<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\Auth0\Internal\FetchUserInfo;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use RxAnte\OAuth\UserInfo\Jwt;
use Throwable;

use function assert;

readonly class GetAuth0ResponseFromSystemCache implements GetAuth0Response
{
    public function __construct(
        private LoggerInterface $logger,
        private CacheItemPoolInterface $cachePool,
        private GetAuth0ResponseFromAuth0 $fromAuth0,
    ) {
    }

    public function get(Jwt $jwt): Auth0Response
    {
        try {
            $response = $this->cachePool->getItem(
                CacheKey::get($jwt),
            )->get();

            assert($response instanceof Auth0Response);

            return $response;
        } catch (Throwable) {
            $this->logger->error(
                'Failed to get $response from CachePool',
            );

            return $this->fromAuth0->get($jwt);
        }
    }
}
