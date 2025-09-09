<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Tests\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse;

use Mockery;
use Mockery\MockInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponseFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponseNoOp;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponseWithSystemCache;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponse;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponseWrapper;

use function describe;
use function expect;
use function it;
use function uses;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('CacheResponseFactory', function (): void {
    uses()->group('CacheResponseFactory');

    readonly class CacheResponseFactoryTestSetup
    {
        public CacheResponseFactory $sut;
        public MockInterface&CacheResponseNoOp $noOp;
        public MockInterface&CacheResponseWithSystemCache $withSystemCache;

        public function __construct()
        {
            $this->noOp = Mockery::mock(
                CacheResponseNoOp::class,
            );

            $this->withSystemCache = Mockery::mock(
                CacheResponseWithSystemCache::class,
            );

            $this->sut = new CacheResponseFactory(
                noOp: $this->noOp,
                withSystemCache: $this->withSystemCache,
            );
        }
    }

    it(
        'returns no-op when response is not valid',
        function (): void {
            $setup = new CacheResponseFactoryTestSetup();

            $response = Mockery::mock(RxAnteResponse::class);
            $response->expects('isNotValid')->andReturnTrue();

            $responseWrapper = new RxAnteResponseWrapper($response);

            $result = $setup->sut->create($responseWrapper);

            expect($result)->toBe($setup->noOp);
        },
    );

    it(
        'returns no-op when response is from cache',
        function (): void {
            $setup = new CacheResponseFactoryTestSetup();

            $response = Mockery::mock(RxAnteResponse::class);
            $response->expects('isNotValid')->andReturnFalse();

            $responseWrapper = new RxAnteResponseWrapper(
                $response,
                isFromCache: true,
            );

            $result = $setup->sut->create($responseWrapper);

            expect($result)->toBe($setup->noOp);
        },
    );

    it(
        'returns system cache when response is valid and not from cache',
        function (): void {
            $setup = new CacheResponseFactoryTestSetup();

            $response = Mockery::mock(RxAnteResponse::class);
            $response->expects('isNotValid')->andReturnFalse();

            $responseWrapper = new RxAnteResponseWrapper(
                $response,
                isFromCache: false,
            );

            $result = $setup->sut->create($responseWrapper);

            expect($result)->toBe($setup->withSystemCache);
        },
    );
});
