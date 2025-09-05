<?php

declare(strict_types=1);

use League\OAuth2\Client\Token\AccessTokenInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use RxAnte\OAuth\TokenRepository\GetAccessTokenBySessionId;
use RxAnte\OAuth\TokenRepository\Refresh\RefreshAccessTokenBySessionId;
use RxAnte\OAuth\TokenRepository\SetAccessTokenFromSessionId;
use RxAnte\OAuth\TokenRepository\TokenRepository;

describe('TokenRepository', function (): void {
    uses()->group('TokenRepository');

    test(
        'createSessionIdWithAccessToken() creates a session id and stores the access token',
        function (): void {
            $uuid = Uuid::fromString(
                'be608702-97d0-4e86-9118-e9fbe7da7778',
            );

            $uuidFactory = Mockery::mock(UuidFactoryInterface::class);
            $uuidFactory->shouldReceive('uuid4')
                ->andReturn($uuid);

            $accessToken = Mockery::mock(AccessTokenInterface::class);

            $setTokenSpy = Mockery::mock(
                SetAccessTokenFromSessionId::class,
            );
            $setTokenSpy->shouldReceive('set')
                ->with(
                    'be608702-97d0-4e86-9118-e9fbe7da7778',
                    $accessToken,
                )
                ->once();

            $repo = new TokenRepository(
                uuidFactory: $uuidFactory,
                getToken: Mockery::mock(
                    GetAccessTokenBySessionId::class,
                ),
                setToken: $setTokenSpy,
                refreshAccessTokenBySessionId: Mockery::mock(
                    RefreshAccessTokenBySessionId::class,
                ),
            );

            $sessionId = $repo->createSessionIdWithAccessToken(
                accessToken: $accessToken,
            );

            expect($sessionId)->toBe(
                'be608702-97d0-4e86-9118-e9fbe7da7778',
            );
        },
    );

    test(
        'setAccessTokenFromSessionId() sets access token from session id',
        function (): void {
            $accessToken = Mockery::mock(AccessTokenInterface::class);

            $setTokenSpy = Mockery::mock(
                SetAccessTokenFromSessionId::class,
            );
            $setTokenSpy->shouldReceive('set')
                ->with('mockId', $accessToken)
                ->once();

            $repo = new TokenRepository(
                uuidFactory: Mockery::mock(UuidFactoryInterface::class),
                getToken: Mockery::mock(
                    GetAccessTokenBySessionId::class,
                ),
                setToken: $setTokenSpy,
                refreshAccessTokenBySessionId: Mockery::mock(
                    RefreshAccessTokenBySessionId::class,
                ),
            );

            $repo->setAccessTokenFromSessionId(
                sessionId: 'mockId',
                accessToken: $accessToken,
            );
        },
    );

    test(
        'getTokenBySessionId() gets token by session id',
        function (): void {
            $accessToken = Mockery::mock(AccessTokenInterface::class);

            $getToken = Mockery::mock(GetAccessTokenBySessionId::class);
            $getToken->shouldReceive('get')
                ->with('mock-sid')
                ->andReturn($accessToken);

            $repo = new TokenRepository(
                uuidFactory: Mockery::mock(UuidFactoryInterface::class),
                getToken: $getToken,
                setToken: Mockery::mock(
                    SetAccessTokenFromSessionId::class,
                ),
                refreshAccessTokenBySessionId: Mockery::mock(
                    RefreshAccessTokenBySessionId::class,
                ),
            );

            $result = $repo->getTokenBySessionId('mock-sid');

            expect($result)->toBe($accessToken);
        },
    );

    test(
        'refreshAccessTokenBySessionId() refreshes access token by session id',
        function (): void {
            $refresh = Mockery::mock(
                RefreshAccessTokenBySessionId::class,
            );
            $refresh->shouldReceive('refresh')
                ->with('foo-sid')
                ->once();

            $repo = new TokenRepository(
                uuidFactory: Mockery::mock(UuidFactoryInterface::class),
                getToken: Mockery::mock(
                    GetAccessTokenBySessionId::class,
                ),
                setToken: Mockery::mock(
                    SetAccessTokenFromSessionId::class,
                ),
                refreshAccessTokenBySessionId: $refresh,
            );

            $repo->refreshAccessTokenBySessionId('foo-sid');
        },
    );
});
