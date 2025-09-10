<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RxAnte\OAuth\Handlers\Common\ProviderOptionsReader;
use RxAnte\OAuth\Handlers\RxAnte\TokenRefresh\RxAnteGetRefreshedAccessToken;
use RxAnte\OAuth\Handlers\RxAnte\WellKnown;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownRepository;

describe('RxAnteGetRefreshedAccessToken', function (): void {
    uses()->group('RxAnteGetRefreshedAccessToken');

    it(
        'returns null when exception occurs',
        function (): void {
            $inputToken = new AccessToken([
                'access_token' => 'mock-access-token',
                'refresh_token' => 'mock-refresh-token',
            ]);

            $wellKnownRepository = Mockery::mock(
                WellKnownRepository::class,
            );
            $wellKnownRepository->expects('get')->andThrow(
                new Exception(),
            );

            $sut = new RxAnteGetRefreshedAccessToken(
                clientFactory: Mockery::mock(ClientFactory::class),
                wellKnownRepository: $wellKnownRepository,
                providerOptionsReader: Mockery::mock(
                    ProviderOptionsReader::class,
                ),
            );

            $result = $sut->get($inputToken);

            expect($result)->toBeNull();
        },
    );

    it(
        'returns null when token response is not 200',
        function (): void {
            $inputToken = new AccessToken([
                'access_token' => 'mock-access-token',
                'refresh_token' => 'mock-refresh-token',
            ]);

            $wellKnown = new WellKnown(
                'mock-issuer',
                'https://auth-endpoint',
                'https://token-endpoint',
                'https://userinfo-endpoint',
            );

            $wellKnownRepository = Mockery::mock(
                WellKnownRepository::class,
            );
            $wellKnownRepository->expects('get')->andReturn(
                $wellKnown,
            );

            $clientRefreshResponse = Mockery::mock(
                ResponseInterface::class,
            );
            $clientRefreshResponse->expects('getStatusCode')
                ->andReturn(500);

            $client = Mockery::mock(Client::class);

            $client->expects('post')->with(
                'https://token-endpoint',
                [
                    RequestOptions::HTTP_ERRORS => false,
                    RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
                    RequestOptions::JSON => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => 'mock-refresh-token',
                        'client_id' => 'mock-client-id',
                        'client_secret' => 'mock-client-secret',
                    ],
                ],
            )->andReturn($clientRefreshResponse);

            $clientFactory = Mockery::mock(ClientFactory::class);
            $clientFactory->expects('create')->andReturn($client);

            $providerOptionsReader = Mockery::mock(
                ProviderOptionsReader::class,
            );
            $providerOptionsReader->expects('clientId')->andReturn(
                'mock-client-id',
            );
            $providerOptionsReader->expects('clientSecret')->andReturn(
                'mock-client-secret',
            );

            $sut = new RxAnteGetRefreshedAccessToken(
                clientFactory: $clientFactory,
                wellKnownRepository: $wellKnownRepository,
                providerOptionsReader: $providerOptionsReader,
            );

            $result = $sut->get($inputToken);

            expect($result)->toBeNull();
        },
    );

    it('returns access token', function (): void {
        $inputToken = new AccessToken([
            'access_token' => 'mock-access-token',
            'refresh_token' => 'mock-refresh-token',
        ]);

        $wellKnown = new WellKnown(
            'mock-issuer',
            'https://auth-endpoint',
            'https://token-endpoint',
            'https://userinfo-endpoint',
        );

        $wellKnownRepository = Mockery::mock(
            WellKnownRepository::class,
        );
        $wellKnownRepository->expects('get')->andReturn(
            $wellKnown,
        );

        $refreshResponseBody = Mockery::mock(StreamInterface::class);
        $refreshResponseBody->expects('__toString')->andReturn(
            (string) json_encode([
                'access_token' => 'mock-new-access-token',
                'refresh_token' => 'mock-new-refresh-token',
                'expires_in' => 12345,
            ]),
        );

        $clientRefreshResponse = Mockery::mock(
            ResponseInterface::class,
        );
        $clientRefreshResponse->expects('getStatusCode')
            ->andReturn(200);
        $clientRefreshResponse->expects('getBody')->andReturn(
            $refreshResponseBody,
        );

        $client = Mockery::mock(Client::class);

        $client->expects('post')->with(
            'https://token-endpoint',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
                RequestOptions::JSON => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => 'mock-refresh-token',
                    'client_id' => 'mock-client-id',
                    'client_secret' => 'mock-client-secret',
                ],
            ],
        )->andReturn($clientRefreshResponse);

        $clientFactory = Mockery::mock(ClientFactory::class);
        $clientFactory->expects('create')->andReturn($client);

        $providerOptionsReader = Mockery::mock(
            ProviderOptionsReader::class,
        );
        $providerOptionsReader->expects('clientId')->andReturn(
            'mock-client-id',
        );
        $providerOptionsReader->expects('clientSecret')->andReturn(
            'mock-client-secret',
        );

        $sut = new RxAnteGetRefreshedAccessToken(
            clientFactory: $clientFactory,
            wellKnownRepository: $wellKnownRepository,
            providerOptionsReader: $providerOptionsReader,
        );

        $result = $sut->get($inputToken);
        assert($result instanceof AccessTokenInterface);

        expect($result->getToken())->toBe(
            'mock-new-access-token',
        );

        expect($result->getExpires())->toBe(time() + 12345);

        expect($result->getRefreshToken())->toBe(
            'mock-new-refresh-token',
        );
    });
});
