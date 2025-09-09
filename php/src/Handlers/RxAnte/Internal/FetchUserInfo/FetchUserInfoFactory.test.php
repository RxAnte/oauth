<?php

declare(strict_types=1);

use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\UnencryptedToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\EmptyJwt;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoForM2M;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoFromRxAnteAuth;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoNoOp;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;

describe('FetchUserInfoFactory', function (): void {
    uses()->group('FetchUserInfoFactory');

    it(
        'returns FetchUserInfoNoOp when empty token',
        function (): void {
            $sut = new FetchUserInfoFactory(
                config: new RxAnteConfig(wellKnownUrl: 'noop'),
                noOp: Mockery::mock(FetchUserInfoNoOp::class),
                forM2M: Mockery::mock(FetchUserInfoForM2M::class),
                fromRxAnteAuth: Mockery::mock(
                    FetchUserInfoFromRxAnteAuth::class,
                ),
            );

            $result = $sut->create(jwt: new EmptyJwt());

            expect($result)->toBeInstanceOf(
                FetchUserInfoNoOp::class,
            );
        },
    );

    it(
        'returns FetchUserInfoForM2M if m2m subject is authorized',
        function (): void {
            $claims = new DataSet(['sub' => 'mock-sub'], '');

            $token = Mockery::mock(UnencryptedToken::class);
            $token->expects('claims')->andReturn($claims);

            $sut = new FetchUserInfoFactory(
                config: new RxAnteConfig(
                    wellKnownUrl: 'noop',
                    m2mAuthorizedSubjects: ['mock-sub'],
                ),
                noOp: Mockery::mock(FetchUserInfoNoOp::class),
                forM2M: Mockery::mock(FetchUserInfoForM2M::class),
                fromRxAnteAuth: Mockery::mock(
                    FetchUserInfoFromRxAnteAuth::class,
                ),
            );

            $result = $sut->create(jwt: $token);

            expect($result)->toBeInstanceOf(
                FetchUserInfoForM2M::class,
            );
        },
    );

    it(
        'returns FetchUserInfoFromRxAnteAuth',
        function (): void {
            $claims = new DataSet(['sub' => 'mock-sub'], '');

            $token = Mockery::mock(UnencryptedToken::class);
            $token->expects('claims')->andReturn($claims);

            $sut = new FetchUserInfoFactory(
                config: new RxAnteConfig(
                    wellKnownUrl: 'noop',
                    m2mAuthorizedSubjects: ['foo-sub'],
                ),
                noOp: Mockery::mock(FetchUserInfoNoOp::class),
                forM2M: Mockery::mock(FetchUserInfoForM2M::class),
                fromRxAnteAuth: Mockery::mock(
                    FetchUserInfoFromRxAnteAuth::class,
                ),
            );

            $result = $sut->create(jwt: $token);

            expect($result)->toBeInstanceOf(
                FetchUserInfoFromRxAnteAuth::class,
            );
        },
    );
});
