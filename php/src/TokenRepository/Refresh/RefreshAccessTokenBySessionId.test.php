<?php

declare(strict_types=1);

use League\OAuth2\Client\Token\AccessTokenInterface;
use Mockery\MockInterface;
use RxAnte\OAuth\TokenRepository\GetAccessTokenBySessionId;
use RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken;
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock;
use RxAnte\OAuth\TokenRepository\Refresh\RefreshAccessTokenBySessionId;
use RxAnte\OAuth\TokenRepository\SetAccessTokenFromSessionId;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('RefreshAccessTokenBySessionId', function (): void {
    uses()->group('RefreshAccessTokenBySessionId');

    class RefreshAccessTokenBySessionIdSetUp
    {
        public readonly RefreshAccessTokenBySessionId $sut;

        public function __construct(
            string $sessionId,
            bool $getReturnsSameToken = false,
            bool|null $refreshedToken = null,
        ) {
            $this->sut = new RefreshAccessTokenBySessionId(
                refreshLock: $this->mockRefreshLock(),
                getToken: $this->mockGetTokenBySessionId(
                    $sessionId,
                    $getReturnsSameToken,
                ),
                setToken: $this->mockSetToken(
                    $refreshedToken,
                    $sessionId,
                ),
                getRefreshedAccessToken: $this->mockGetRefreshedAccessToken(
                    $refreshedToken,
                ),
            );
        }

        private function mockRefreshLock(): RefreshLock&MockInterface
        {
            $refreshLock = Mockery::mock(RefreshLock::class);

            $refreshLock->expects('acquire')
                ->with('foo-mock-token-1')
                ->ordered();

            $refreshLock->expects('release')
                ->with('foo-mock-token-1')
                ->ordered();

            return $refreshLock;
        }

        private int $getCallCount = 0;

        private function mockGetTokenBySessionId(
            string $sessionId,
            bool $getReturnsSameToken,
        ): GetAccessTokenBySessionId&MockInterface {
            $accessToken1 = Mockery::mock(AccessTokenInterface::class);

            $accessToken1->allows('getToken')
                ->andReturn('foo-mock-token-1');

            $accessToken2 = Mockery::mock(AccessTokenInterface::class);

            $accessToken2->allows('getToken')
                ->andReturn('foo-mock-token-2');

            $getToken = Mockery::mock(
                GetAccessTokenBySessionId::class,
            );

            $getToken->allows('get')
                ->andReturnUsing(function (
                    string $argSessionId,
                ) use (
                    $sessionId,
                    $getReturnsSameToken,
                    $accessToken1,
                    $accessToken2,
                ): AccessTokenInterface {
                    $this->getCallCount += 1;

                    expect($argSessionId)->toBe($sessionId);

                    if ($getReturnsSameToken) {
                        return $accessToken1;
                    }

                    if ($this->getCallCount === 1) {
                        return $accessToken1;
                    }

                    return $accessToken2;
                });

            return $getToken;
        }

        private function mockGetRefreshedAccessToken(
            bool|null $refreshedToken,
        ): GetRefreshedAccessToken&MockInterface {
            $getRefreshedAccessToken = Mockery::mock(
                GetRefreshedAccessToken::class,
            );

            if ($refreshedToken === null) {
                return $getRefreshedAccessToken;
            }

            if ($refreshedToken === false) {
                $getRefreshedAccessToken->expects('get')
                    ->andReturnUsing(
                        function (AccessTokenInterface $token): null {
                            expect($token->getToken())->toBe(
                                'foo-mock-token-1',
                            );

                            return null;
                        },
                    );

                return $getRefreshedAccessToken;
            }

            $newToken = Mockery::mock(AccessTokenInterface::class);

            $newToken->allows('getToken')
                ->andReturn('mock-new-token');

            $getRefreshedAccessToken->expects('get')
                ->andReturnUsing(
                    function (
                        AccessTokenInterface $token,
                    ) use ($newToken): AccessTokenInterface {
                        expect($token->getToken())->toBe(
                            'foo-mock-token-1',
                        );

                        return $newToken;
                    },
                );

            return $getRefreshedAccessToken;
        }

        private function mockSetToken(
            bool|null $refreshedToken,
            string $sessionId,
        ): SetAccessTokenFromSessionId&MockInterface {
            $setToken = Mockery::mock(
                SetAccessTokenFromSessionId::class,
            );

            if ($refreshedToken !== true) {
                return $setToken;
            }

            $setToken->expects('set')
                ->andReturnUsing(
                    function (
                        string $sessionIdArg,
                        AccessTokenInterface $newToken,
                    ) use ($sessionId): void {
                        expect($sessionIdArg)->toBe(
                            $sessionId,
                        );

                        expect($newToken->getToken())->toBe(
                            'mock-new-token',
                        );
                    },
                );

            return $setToken;
        }
    }

    it(
        'does not refresh the token if it was already refreshed while awaiting a lock',
        function (): void {
            $setUp = new RefreshAccessTokenBySessionIdSetUp(
                sessionId: 'foo-id-123',
            );

            $setUp->sut->refresh(sessionId: 'foo-id-123');
        },
    );

    it(
        'attempts to refresh the token and releases the lock if there was no new token',
        function (): void {
            $setUp = new RefreshAccessTokenBySessionIdSetUp(
                sessionId: 'foo-id-456',
                getReturnsSameToken: true,
                refreshedToken: false,
            );

            $setUp->sut->refresh(sessionId: 'foo-id-456');
        },
    );

    it(
        'refreshes the token and releases the lock',
        function (): void {
            $setUp = new RefreshAccessTokenBySessionIdSetUp(
                sessionId: 'foo-id-789',
                getReturnsSameToken: true,
                refreshedToken: true,
            );

            $setUp->sut->refresh(sessionId: 'foo-id-789');
        },
    );
});
