<?php

declare(strict_types=1);

use League\OAuth2\Client\Provider\AbstractProvider;
use RxAnte\OAuth\Handlers\RxAnte\WellKnown;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactory;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactoryConfig;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownRepository;
use RxAnte\OAuth\Routes\RoutesFactory;

describe('WellKnownProviderFactory', function (): void {
    uses()->group('WellKnownProviderFactory');

    it(
        'creates provider without audience',
        function (): void {
            $wellKnown = new WellKnown(
                issuer: 'mock-issuer',
                authorizationEndpoint: 'https://mock-auth-endpoint',
                tokenEndpoint: 'https://mock-token-endpoint',
                userinfoEndpoint: 'mock-userinfo-endpoint',
            );

            $wellKnownRepository = Mockery::mock(
                WellKnownRepository::class,
            );
            $wellKnownRepository->expects('get')->andReturn(
                $wellKnown,
            );

            $sut = new WellKnownProviderFactory(
                routesFactory: new RoutesFactory(),
                config: new WellKnownProviderFactoryConfig(
                    appBaseUrl: 'https://mock-app-base-url',
                    clientId: 'mock-client-id',
                    clientSecret: 'mock-client-secret',
                ),
                wellKnownRepository: $wellKnownRepository,
            );

            $result = $sut->create();

            $authUrl = $result->getAuthorizationUrl(
                ['state' => 'mock-state'],
            );

            $parsedUrl = parse_url($authUrl);

            /** @phpstan-ignore-next-line */
            expect($parsedUrl['scheme'])->toBe('https');

            /** @phpstan-ignore-next-line */
            expect($parsedUrl['host'])->toBe(
                'mock-auth-endpoint',
            );

            /** @phpstan-ignore-next-line */
            parse_str($parsedUrl['query'], $parsedQuery);

            expect($parsedQuery['state'])->toBe('mock-state');

            expect($parsedQuery['scope'])->toBe(
                'openid profile email offline_access',
            );

            expect($parsedQuery['response_type'])->toBe(
                'code',
            );

            expect($parsedQuery['approval_prompt'])->toBe(
                'auto',
            );

            expect($parsedQuery['code_challenge'])->not->toBeEmpty();

            expect($parsedQuery['code_challenge'])->toHaveLength(
                43,
            );

            expect($parsedQuery['code_challenge_method'])->toBe(
                AbstractProvider::PKCE_METHOD_S256,
            );

            expect($parsedQuery['redirect_uri'])->toBe(
                'https://mock-app-base-url/auth/callback',
            );

            expect($parsedQuery['client_id'])->toBe(
                'mock-client-id',
            );

            $reflection = new ReflectionClass($result);
            $property   = $reflection->getProperty('clientSecret');
            $property->setAccessible(true);
            $clientSecret = $property->getValue($result);
            expect($clientSecret)->toBe('mock-client-secret');

            expect($result->getBaseAccessTokenUrl([]))->toBe(
                'https://mock-token-endpoint',
            );
        },
    );

    it(
        'creates provider with audience',
        function (): void {
            $wellKnown = new WellKnown(
                issuer: 'mock-issuer',
                authorizationEndpoint: 'https://mock-auth-endpoint-foo',
                tokenEndpoint: 'https://mock-token-endpoint-foo-bar',
                userinfoEndpoint: 'mock-userinfo-endpoint',
            );

            $wellKnownRepository = Mockery::mock(
                WellKnownRepository::class,
            );
            $wellKnownRepository->expects('get')->andReturn(
                $wellKnown,
            );

            $sut = new WellKnownProviderFactory(
                routesFactory: new RoutesFactory(),
                config: new WellKnownProviderFactoryConfig(
                    appBaseUrl: 'https://mock-app-base-url-bar',
                    clientId: 'mock-client-id-foo',
                    clientSecret: 'mock-client-secret-bar',
                    scopes: ['foo'],
                    audience: 'mock-audience',
                ),
                wellKnownRepository: $wellKnownRepository,
            );

            $result = $sut->create();

            $authUrl = $result->getAuthorizationUrl(
                ['state' => 'mock-state-foo'],
            );

            $parsedUrl = parse_url($authUrl);

            /** @phpstan-ignore-next-line */
            expect($parsedUrl['scheme'])->toBe('https');

            /** @phpstan-ignore-next-line */
            expect($parsedUrl['host'])->toBe(
                'mock-auth-endpoint-foo',
            );

            /** @phpstan-ignore-next-line */
            parse_str($parsedUrl['query'], $parsedQuery);

            expect($parsedQuery['state'])->toBe(
                'mock-state-foo',
            );

            expect($parsedQuery['scope'])->toBe(
                'foo',
            );

            expect($parsedQuery['response_type'])->toBe(
                'code',
            );

            expect($parsedQuery['approval_prompt'])->toBe(
                'auto',
            );

            expect($parsedQuery['code_challenge'])->not->toBeEmpty();

            expect($parsedQuery['code_challenge'])->toHaveLength(
                43,
            );

            expect($parsedQuery['code_challenge_method'])->toBe(
                AbstractProvider::PKCE_METHOD_S256,
            );

            expect($parsedQuery['redirect_uri'])->toBe(
                'https://mock-app-base-url-bar/auth/callback',
            );

            expect($parsedQuery['client_id'])->toBe(
                'mock-client-id-foo',
            );

            $reflection = new ReflectionClass($result);
            $property   = $reflection->getProperty('clientSecret');
            $property->setAccessible(true);
            $clientSecret = $property->getValue($result);
            expect($clientSecret)->toBe(
                'mock-client-secret-bar',
            );

            expect($result->getBaseAccessTokenUrl([]))->toBe(
                'https://mock-token-endpoint-foo-bar',
            );
        },
    );
});
