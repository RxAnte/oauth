<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Tests\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse;

use DateTimeImmutable;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Mockery;
use Mockery\MockInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheKey;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponseWithSystemCache;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponse;

use function describe;
use function it;
use function uses;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('CacheResponseWithSystemCache', function (): void {
    uses()->group('CacheResponseWithSystemCache');

    readonly class CacheResponseWithSystemCacheTestSetup
    {
        public CacheResponseWithSystemCache $sut;
        public MockInterface&CacheItemPoolInterface $cachePool;

        public function __construct()
        {
            $this->cachePool = Mockery::mock(
                CacheItemPoolInterface::class,
            );

            $this->sut = new CacheResponseWithSystemCache(
                cachePool: $this->cachePool,
            );
        }
    }

    it(
        'does not cache the response if the jwt has no expiration',
        function (): void {
            $setup = new CacheResponseWithSystemCacheTestSetup();

            $response = Mockery::mock(RxAnteResponse::class);

            $jwt = Mockery::mock(JwtToken::class);
            $jwt->expects('claims')->andReturn(new DataSet(
                ['exp' => null],
                '',
            ));

            $setup->sut->cache($jwt, $response);
        },
    );

    it(
        'caches the response with an expiration date from the jwt',
        function (): void {
            $setup = new CacheResponseWithSystemCacheTestSetup();

            $response = Mockery::mock(RxAnteResponse::class);

            $expiration = new DateTimeImmutable('+1 hour');

            $cacheItem = Mockery::mock(CacheItemInterface::class);
            $cacheItem->expects('set')
                ->with($response)
                ->andReturnSelf();
            $cacheItem->expects('expiresAt')
                ->with($expiration)
                ->andReturnSelf();

            $jwt = Mockery::mock(JwtToken::class);
            $jwt->expects('claims')->andReturn(new DataSet(
                ['exp' => $expiration],
                '',
            ));
            $jwt->allows('toString')->andReturn('mock-jwt-string');

            $setup->cachePool
                ->expects('getItem')
                ->with(CacheKey::get($jwt))
                ->andReturn($cacheItem);

            $setup->cachePool->expects('save')
                ->with($cacheItem);

            $setup->sut->cache($jwt, $response);
        },
    );
});
