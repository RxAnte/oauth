<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheKey;
use RxAnte\OAuth\NoOpLogger;
use Throwable;

use function assert;

readonly class GetRxAnteResponseFromSystemCache implements GetRxAnteResponse
{
    private LoggerInterface $logger;

    public function __construct(
        ContainerInterface $di,
        private CacheItemPoolInterface $cachePool,
        private GetRxAnteResponseFromRxAnte $fromRxAnte,
    ) {
        if ($di->has(LoggerInterface::class)) {
            $this->logger = $di->get(LoggerInterface::class);

            return;
        }

        $this->logger = new NoOpLogger();
    }

    public function get(JwtToken $jwt): RxAnteResponseWrapper
    {
        try {
            $response = $this->cachePool->getItem(
                CacheKey::get($jwt),
            )->get();

            assert($response instanceof RxAnteResponse);

            return new RxAnteResponseWrapper(
                $response,
                true,
            );
        } catch (Throwable) {
            $this->logger->error(
                'Failed to get $response from CachePool',
            );

            return $this->fromRxAnte->get($jwt);
        }
    }
}
