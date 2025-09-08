<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;
use RxAnte\OAuth\Handlers\RxAnte\WellKnown;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownRepository;

describe('WellKnownRepository', function (): void {
    uses()->group('WellKnownRepository');

    it(
        'returns WellKnown from cache',
        function (): void {
            $config = new RxAnteConfig(
                wellKnownUrl: 'https://auth-server/well-known',
            );

            $wellKnown = Mockery::mock(WellKnown::class);

            $cacheItem = Mockery::mock(CacheItemInterface::class);
            $cacheItem->expects('isHit')->andReturnTrue();
            $cacheItem->expects('get')->andReturn($wellKnown);

            $cachePool = Mockery::mock(CacheItemPoolInterface::class);
            $cachePool->expects('getItem')
                ->with(implode('_', [
                    $config->wellKnownCacheKey,
                    md5($config->wellKnownUrl),
                ]))
                ->andReturn($cacheItem);

            $sut = new WellKnownRepository(
                config: $config,
                clientFactory: Mockery::mock(ClientFactory::class),
                cachePool: $cachePool,
            );

            expect($sut->get())->toBe($wellKnown);
        },
    );

    it(
        'throws an exception on non-200 HTTP response',
        function (): void {
            $config = new RxAnteConfig(
                wellKnownUrl: 'https://auth-server/well-known',
            );

            $cacheItem = Mockery::mock(CacheItemInterface::class);
            $cacheItem->expects('isHit')->andReturnFalse();

            $cachePool = Mockery::mock(CacheItemPoolInterface::class);
            $cachePool->expects('getItem')
                ->with(implode('_', [
                    $config->wellKnownCacheKey,
                    md5($config->wellKnownUrl),
                ]))
                ->andReturn($cacheItem);

            $response = Mockery::mock(ResponseInterface::class);
            $response->expects('getStatusCode')->andReturn(555);

            $client = Mockery::mock(Client::class);
            $client->expects('get')
                ->with($config->wellKnownUrl, [
                    RequestOptions::HEADERS => ['Accept' => 'application/json'],
                    RequestOptions::HTTP_ERRORS => false,
                ])
                ->andReturn($response);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn($client);

            $sut = new WellKnownRepository(
                config: $config,
                clientFactory: $clientFactory,
                cachePool: $cachePool,
            );

            try {
                $sut->get();
                expect(true)->toBeFalse('Exception not thrown');
            } catch (RuntimeException $exception) {
                expect($exception->getCode())->toBe(555);

                expect($exception->getMessage())->toBe(
                    implode(' ', [
                        'Unable to get WellKnown from',
                        $config->wellKnownUrl . '.',
                        'Well Known URL returned a non-200 status code.',
                    ]),
                );
            }
        },
    );

    it(
        'throws an exception when HTTP response contains invalid JSON',
        function (): void {
            $config = new RxAnteConfig(
                wellKnownUrl: 'https://auth-server/well-known',
            );

            $cacheItem = Mockery::mock(CacheItemInterface::class);
            $cacheItem->expects('isHit')->andReturnFalse();

            $cachePool = Mockery::mock(CacheItemPoolInterface::class);
            $cachePool->expects('getItem')
                ->with(implode('_', [
                    $config->wellKnownCacheKey,
                    md5($config->wellKnownUrl),
                ]))
                ->andReturn($cacheItem);

            $responseBody = Mockery::mock(StreamInterface::class);
            $responseBody->expects('__toString')->andReturn(
                '{"foo": bar}',
            );

            $response = Mockery::mock(ResponseInterface::class);
            $response->expects('getStatusCode')->andReturn(200);
            $response->expects('getBody')->andReturn($responseBody);

            $client = Mockery::mock(Client::class);
            $client->expects('get')
                ->with($config->wellKnownUrl, [
                    RequestOptions::HEADERS => ['Accept' => 'application/json'],
                    RequestOptions::HTTP_ERRORS => false,
                ])
                ->andReturn($response);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn($client);

            $sut = new WellKnownRepository(
                config: $config,
                clientFactory: $clientFactory,
                cachePool: $cachePool,
            );

            try {
                $sut->get();
                expect(true)->toBeFalse('Exception not thrown');
            } catch (RuntimeException $exception) {
                expect($exception->getMessage())->toBe(
                    implode(' ', [
                        'Unable to get WellKnown from',
                        $config->wellKnownUrl . '.',
                        'Well Known response is not valid JSON.',
                    ]),
                );
            }
        },
    );

    it(
        'throws an exception when required fields are missing in JSON response',
        function (): void {
            function WellKnownRepositoryTestMissingJsonResponse(
                string $json,
            ): void {
                $config = new RxAnteConfig(
                    wellKnownUrl: 'https://auth-server/well-known',
                );

                $cacheItem = Mockery::mock(CacheItemInterface::class);
                $cacheItem->expects('isHit')->andReturnFalse();

                $cachePool = Mockery::mock(
                    CacheItemPoolInterface::class,
                );
                $cachePool->expects('getItem')
                    ->with(implode('_', [
                        $config->wellKnownCacheKey,
                        md5($config->wellKnownUrl),
                    ]))
                    ->andReturn($cacheItem);

                $responseBody = Mockery::mock(StreamInterface::class);
                $responseBody->expects('__toString')->andReturn(
                    $json,
                );

                $response = Mockery::mock(ResponseInterface::class);
                $response->expects('getStatusCode')->andReturn(200);
                $response->expects('getBody')->andReturn(
                    $responseBody,
                );

                $client = Mockery::mock(Client::class);
                $client->expects('get')
                    ->with($config->wellKnownUrl, [
                        RequestOptions::HEADERS => ['Accept' => 'application/json'],
                        RequestOptions::HTTP_ERRORS => false,
                    ])
                    ->andReturn($response);

                $clientFactory = Mockery::mock(ClientFactory::class);
                $clientFactory->expects('create')->andReturn($client);

                $sut = new WellKnownRepository(
                    config: $config,
                    clientFactory: $clientFactory,
                    cachePool: $cachePool,
                );

                try {
                    $sut->get();
                    expect(true)->toBeFalse('Exception not thrown');
                } catch (RuntimeException $exception) {
                    expect($exception->getMessage())->toBe(
                        implode(' ', [
                            'Unable to get WellKnown from',
                            $config->wellKnownUrl . '.',
                            'Well Known response JSON does not contain needed data.',
                        ]),
                    );
                }
            }

            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode(['foo' => 'bar']),
            );
            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 1,
                ]),
            );
            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 'mock-issuer',
                ]),
            );
            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 'mock-issuer',
                    'authorization_endpoint' => 2,
                ]),
            );
            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 'mock-issuer',
                    'authorization_endpoint' => 'mock-authorization_endpoint',
                ]),
            );
            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 'mock-issuer',
                    'authorization_endpoint' => 'mock-authorization_endpoint',
                    'token_endpoint' => 3,
                ]),
            );
            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 'mock-issuer',
                    'authorization_endpoint' => 'mock-authorization_endpoint',
                    'token_endpoint' => 'mock-token_endpoint',
                ]),
            );
            WellKnownRepositoryTestMissingJsonResponse(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 'mock-issuer',
                    'authorization_endpoint' => 'mock-authorization_endpoint',
                    'token_endpoint' => 'mock-token_endpoint',
                    'userinfo_endpoint' => 4,
                ]),
            );
        },
    );

    it(
        'fetches and caches WellKnown',
        function (): void {
            $config = new RxAnteConfig(
                wellKnownUrl: 'https://auth-server/well-known',
            );

            $cacheItem = Mockery::mock(CacheItemInterface::class);
            $cacheItem->expects('isHit')->andReturnFalse();
            $cacheItem->expects('set')->andReturnUsing(
                function (WellKnown $wellKnown) use (
                    $cacheItem,
                ): CacheItemInterface {
                    expect($wellKnown->issuer)->toBe(
                        'mock-issuer',
                    );

                    expect($wellKnown->authorizationEndpoint)->toBe(
                        'mock-authorization_endpoint',
                    );

                    expect($wellKnown->tokenEndpoint)->toBe(
                        'mock-token_endpoint',
                    );

                    expect($wellKnown->userinfoEndpoint)->toBe(
                        'mock-userinfo_endpoint',
                    );

                    return $cacheItem;
                },
            );
            $cacheItem->expects('expiresAfter')->andReturnUsing(
                function (DateInterval $dateInterval) use (
                    $cacheItem,
                ): CacheItemInterface {
                    /** @phpstan-ignore-next-line */
                    $totalSeconds = ($dateInterval->days * 24 * 60 * 60)
                        + ($dateInterval->h * 60 * 60)
                        + ($dateInterval->i * 60)
                        + $dateInterval->s;

                    expect($totalSeconds)->toBe(86400);

                    return $cacheItem;
                },
            );

            $cachePool = Mockery::mock(
                CacheItemPoolInterface::class,
            );
            $cachePool->expects('getItem')
                ->with(implode('_', [
                    $config->wellKnownCacheKey,
                    md5($config->wellKnownUrl),
                ]))
                ->andReturn($cacheItem);
            $cachePool->expects('save')->andReturnUsing(
                function (CacheItemInterface $argCacheItem) use (
                    $cacheItem,
                ): bool {
                    expect($argCacheItem)->toBe($cacheItem);

                    return true;
                },
            );

            $responseBody = Mockery::mock(StreamInterface::class);
            $responseBody->expects('__toString')->andReturn(
                (string) json_encode([
                    'foo' => 'bar',
                    'issuer' => 'mock-issuer',
                    'authorization_endpoint' => 'mock-authorization_endpoint',
                    'token_endpoint' => 'mock-token_endpoint',
                    'userinfo_endpoint' => 'mock-userinfo_endpoint',
                ]),
            );

            $response = Mockery::mock(ResponseInterface::class);
            $response->expects('getStatusCode')->andReturn(200);
            $response->expects('getBody')->andReturn(
                $responseBody,
            );

            $client = Mockery::mock(Client::class);
            $client->expects('get')
                ->with($config->wellKnownUrl, [
                    RequestOptions::HEADERS => ['Accept' => 'application/json'],
                    RequestOptions::HTTP_ERRORS => false,
                ])
                ->andReturn($response);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn($client);

            $sut = new WellKnownRepository(
                config: $config,
                clientFactory: $clientFactory,
                cachePool: $cachePool,
            );

            $result = $sut->get();

            expect($result->issuer)->toBe(
                'mock-issuer',
            );

            expect($result->authorizationEndpoint)->toBe(
                'mock-authorization_endpoint',
            );

            expect($result->tokenEndpoint)->toBe(
                'mock-token_endpoint',
            );

            expect($result->userinfoEndpoint)->toBe(
                'mock-userinfo_endpoint',
            );
        },
    );
});
