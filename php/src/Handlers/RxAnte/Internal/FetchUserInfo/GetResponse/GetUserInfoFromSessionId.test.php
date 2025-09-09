<?php

declare(strict_types=1);

use Lcobucci\JWT\UnencryptedToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Mockery\MockInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfo;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetUserInfoFromSessionId;
use RxAnte\OAuth\Handlers\RxAnte\Internal\JwtFactory;
use RxAnte\OAuth\TokenRepository\EmptyAccessToken;
use RxAnte\OAuth\TokenRepository\TokenRepository;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification

describe('GetUserInfoFromSessionId', function (): void {
    uses()->group('GetUserInfoFromSessionId');

    readonly class GetUserInfoFromSessionIdTestSetup
    {
        public GetUserInfoFromSessionId $sut;
        public MockInterface&JwtFactory $jwtFactory;
        public MockInterface&TokenRepository $tokenRepository;
        public MockInterface&FetchUserInfoFactory $fetchUserInfoFactory;

        public function __construct()
        {
            $this->jwtFactory = Mockery::mock(JwtFactory::class);

            $this->tokenRepository = Mockery::mock(
                TokenRepository::class,
            );

            $this->fetchUserInfoFactory = Mockery::mock(
                FetchUserInfoFactory::class,
            );

            $this->sut = new GetUserInfoFromSessionId(
                jwtFactory: $this->jwtFactory,
                tokenRepository: $this->tokenRepository,
                fetchUserInfoFactory: $this->fetchUserInfoFactory,
            );
        }
    }

    it(
        'returns empty user info if session id is null',
        function (): void {
            $setup = new GetUserInfoFromSessionIdTestSetup();

            $result = $setup->sut->get(null);

            expect($result->isValid)->toBeFalse();
        },
    );

    it(
        'returns empty user info if session id is an empty string',
        function (): void {
            $setup = new GetUserInfoFromSessionIdTestSetup();

            $result = $setup->sut->get('');

            expect($result->isValid)->toBeFalse();
        },
    );

    it(
        'returns empty user info if token is not found',
        function (): void {
            $setup = new GetUserInfoFromSessionIdTestSetup();

            $setup->tokenRepository
                ->expects('getTokenBySessionId')
                ->with('mock-session-id')
                ->andReturn(new EmptyAccessToken());

            $result = $setup->sut->get('mock-session-id');

            expect($result->isValid)->toBeFalse();
        },
    );

    it(
        'returns user info when a valid token is found',
        function (): void {
            $setup = new GetUserInfoFromSessionIdTestSetup();

            $accessToken = Mockery::mock(AccessTokenInterface::class);
            $accessToken->allows('getToken')->andReturn('mock-token');

            $setup->tokenRepository
                ->expects('getTokenBySessionId')
                ->with('mock-id')
                ->andReturn($accessToken);

            $jwtToken = Mockery::mock(UnencryptedToken::class);

            $setup->jwtFactory
                ->expects('createFromToken')
                ->with('mock-token')
                ->andReturn($jwtToken);

            $userInfo = new OauthUserInfo(isValid: true);

            $fetchUserInfo = Mockery::mock(FetchUserInfo::class);
            $fetchUserInfo->expects('fetch')
                ->with($jwtToken)
                ->andReturn($userInfo);

            $setup->fetchUserInfoFactory->expects('create')
                ->with($jwtToken)
                ->andReturn($fetchUserInfo);

            $result = $setup->sut->get('mock-id');

            expect($result)->toBe($userInfo);

            expect($userInfo->isValid)->toBeTrue();
        },
    );
});
