<?php

declare(strict_types=1);

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Mockery\MockInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFromRxAnte;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFromSystemCache;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponse;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponseWrapper;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('GetRxAnteResponseFromSystemCache', function (): void {
    uses()->group('GetRxAnteResponseFromSystemCache');

    readonly class GetRxAnteResponseFromSystemCacheTestSetup
    {
        public GetRxAnteResponseFromSystemCache $sut;
        public MockInterface&ContainerInterface $di;
        public MockInterface&CacheItemPoolInterface $cachePool;
        public MockInterface&GetRxAnteResponseFromRxAnte $fromRxAnte;
        public MockInterface&LoggerInterface $logger;

        public function __construct(bool $enableDiLogger = false)
        {
            $this->di = Mockery::mock(ContainerInterface::class);

            $this->cachePool = Mockery::mock(
                CacheItemPoolInterface::class,
            );

            $this->fromRxAnte = Mockery::mock(
                GetRxAnteResponseFromRxAnte::class,
            );

            $this->logger = Mockery::mock(LoggerInterface::class);

            $this->di->allows('has')
                ->with(LoggerInterface::class)
                ->andReturn($enableDiLogger);

            $this->di->allows('get')
                ->with(LoggerInterface::class)
                ->andReturn($this->logger);

            $this->sut = new GetRxAnteResponseFromSystemCache(
                di: $this->di,
                cachePool: $this->cachePool,
                fromRxAnte: $this->fromRxAnte,
            );
        }
    }

    it(
        'returns a response from cache on cache hit',
        function (): void {
            $setup = new GetRxAnteResponseFromSystemCacheTestSetup();

            $jwt = Mockery::mock(JwtToken::class);
            $jwt->expects('toString')->andReturn('mock-jwt-string');

            $cacheItem = Mockery::mock(CacheItemInterface::class);

            $cachedResponse = Mockery::mock(RxAnteResponse::class);

            $setup->cachePool
                ->expects('getItem')
                ->with('rxante_auth_user_info_responsea45c1b7742c82ac7ad172557c1506d7a')
                ->andReturn($cacheItem);

            $cacheItem->expects('get')->andReturn($cachedResponse);

            $setup->fromRxAnte->shouldNotReceive('get');

            $result = $setup->sut->get($jwt);

            expect($result->response)->toBe($cachedResponse);

            expect($result->isFromCache)->toBeTrue();
        },
    );

    it(
        'falls back to GetRxAnteResponseFromRxAnte and logs error on cache miss',
        function (): void {
            $setup = new GetRxAnteResponseFromSystemCacheTestSetup(
                true,
            );

            $jwt = Mockery::mock(JwtToken::class);
            $jwt->expects('toString')->andReturn('mock-jwt-string');

            $cacheItem = Mockery::mock(CacheItemInterface::class);

            $fallbackResponse = Mockery::mock(
                RxAnteResponseWrapper::class,
            );

            $setup->cachePool
                ->expects('getItem')
                ->with('rxante_auth_user_info_responsea45c1b7742c82ac7ad172557c1506d7a')
                ->andReturn($cacheItem);

            $cacheItem->expects('get')->andThrow(
                new Exception('Cache failed'),
            );

            $setup->logger
                ->expects('error')
                ->with('Failed to get $response from CachePool');

            $setup->fromRxAnte
                ->expects('get')
                ->with($jwt)
                ->andReturn($fallbackResponse);

            $result = $setup->sut->get($jwt);

            expect($result)->toBe($fallbackResponse);
        },
    );
});
