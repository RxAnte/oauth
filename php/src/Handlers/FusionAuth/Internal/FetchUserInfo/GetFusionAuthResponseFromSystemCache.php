<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\FusionAuth\Internal\FetchUserInfo;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use RxAnte\OAuth\UserInfo\Jwt;
use Throwable;

use function assert;

/** @deprecated We're moving to the more generic handler in the RxAnte namespace */
readonly class GetFusionAuthResponseFromSystemCache implements GetFusionAuthResponse
{
    public function __construct(
        private LoggerInterface $logger,
        private CacheItemPoolInterface $cachePool,
        private GetFusionAuthResponseFromFusionAuth $fromFusionAuth,
    ) {
    }

    public function get(Jwt $jwt): FusionAuthResponse
    {
        try {
            $response = $this->cachePool->getItem(
                CacheKey::get($jwt),
            )->get();

            assert($response instanceof FusionAuthResponse);

            return $response;
        } catch (Throwable) {
            $this->logger->error(
                'Failed to get $response from CachePool',
            );

            return $this->fromFusionAuth->get($jwt);
        }
    }
}
