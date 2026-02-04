<?php

declare(strict_types=1);

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Mockery\MockInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFromRxAnte;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFromSystemCache;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\UserInfoFetchLock;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\UserInfoFetchNoOp;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('GetRxAnteResponseFactory', function (): void {
    uses()->group('GetRxAnteResponseFactory');

    readonly class GetRxAnteResponseFactoryTestSetup
    {
        public GetRxAnteResponseFactory $sut;
        public MockInterface&UserInfoFetchNoOp $userInfoFetchNoOp;
        public MockInterface&ContainerInterface $di;
        public MockInterface&CacheItemPoolInterface $cachePool;
        public MockInterface&GetRxAnteResponseFromRxAnte $fromRxAnte;
        public MockInterface&GetRxAnteResponseFromSystemCache $fromCache;

        public function __construct(
            UserInfoFetchLock|null $userInfoFetchLock = null,
        ) {
            $this->userInfoFetchNoOp = Mockery::mock(UserInfoFetchNoOp::class);
            $this->di                = Mockery::mock(ContainerInterface::class);
            $this->cachePool         = Mockery::mock(CacheItemPoolInterface::class);
            $this->fromRxAnte        = Mockery::mock(GetRxAnteResponseFromRxAnte::class);
            $this->fromCache         = Mockery::mock(GetRxAnteResponseFromSystemCache::class);

            $this->di
                ->allows('has')
                ->with(UserInfoFetchLock::class)
                ->andReturn($userInfoFetchLock !== null);

            $this->di
                ->allows('get')
                ->with(UserInfoFetchLock::class)
                ->andReturn($userInfoFetchLock);

            $this->di
                ->allows('get')
                ->with(UserInfoFetchNoOp::class)
                ->andReturn($this->userInfoFetchNoOp);

            $this->sut = new GetRxAnteResponseFactory(
                di: $this->di,
                cachePool: $this->cachePool,
                fromRxAnte: $this->fromRxAnte,
                fromCache: $this->fromCache,
            );
        }
    }

    it(
        'returns GetRxAnteResponseFromSystemCache when item is in cache',
        function (): void {
            $setup = new GetRxAnteResponseFactoryTestSetup();

            $jwt = Mockery::mock(JwtToken::class);

            $jwt->expects('toString')->andReturn('mock-jwt');

            $setup->cachePool
                ->expects('hasItem')
                ->with('rxante_auth_user_info_response4186253e8404eecfb68841ad43040bfa')
                ->andReturn(true);

            $setup->userInfoFetchNoOp->shouldNotHaveBeenCalled();

            $result = $setup->sut->create($jwt);

            expect($result)->toBe($setup->fromCache);
        },
    );

    it(
        'returns GetRxAnteResponseFromSystemCache when item is in cache after acquiring lock',
        function (): void {
            $setup = new GetRxAnteResponseFactoryTestSetup();

            $jwt = Mockery::mock(JwtToken::class);

            $jwt->expects('toString')->andReturn('mock-jwt-string');

            $setup->cachePool
                ->expects('hasItem')
                ->twice()
                ->with('rxante_auth_user_info_responsea45c1b7742c82ac7ad172557c1506d7a')
                ->andReturn(false, true);

            $setup->userInfoFetchNoOp
                ->expects('acquire')
                ->with('rxante_auth_user_info_responsea45c1b7742c82ac7ad172557c1506d7a');

            $setup->userInfoFetchNoOp
                ->expects('release')
                ->with('rxante_auth_user_info_responsea45c1b7742c82ac7ad172557c1506d7a');

            $result = $setup->sut->create($jwt);

            expect($result)->toBe($setup->fromCache);
        },
    );

    it(
        'returns GetRxAnteResponseFromRxAnte when item is not in cache',
        function (): void {
            $userInfoFetchLock = Mockery::mock(UserInfoFetchLock::class);

            $userInfoFetchLock->expects('acquire')
                ->with('rxante_auth_user_info_responsea45c1b7742c82ac7ad172557c1506d7a');

            $setup = new GetRxAnteResponseFactoryTestSetup($userInfoFetchLock);

            $jwt = Mockery::mock(JwtToken::class);

            $jwt->expects('toString')->andReturn('mock-jwt-string');

            $setup->cachePool
                ->expects('hasItem')
                ->twice()
                ->with('rxante_auth_user_info_responsea45c1b7742c82ac7ad172557c1506d7a')
                ->andReturn(false);

            $setup->userInfoFetchNoOp->shouldNotHaveBeenCalled();

            $result = $setup->sut->create($jwt);

            expect($result)->toBe($setup->fromRxAnte);
        },
    );
});
