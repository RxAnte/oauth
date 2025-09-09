<?php

declare(strict_types=1);

use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\UnencryptedToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoForM2M;

describe('FetchUserInfoForM2M', function (): void {
    uses()->group('FetchUserInfoForM2M');

    it(
        'gets user info when sub is not string',
        function (): void {
            $claims = new DataSet([], '');

            $token = Mockery::mock(UnencryptedToken::class);
            $token->expects('claims')->andReturn($claims);

            $sut = new FetchUserInfoForM2M();

            $result = $sut->fetch($token);

            expect($result->isValid)->toBeTrue();

            expect($result->sub)->toBe('m2m');

            expect($result->email)->toBe('m2m@m2m');

            expect($result->name)->toBe('m2m');

            expect($result->givenName)->toBe('m2m');

            expect($result->familyName)->toBe('m2m');

            expect($result->picture)->toBe('');

            expect($result->roles)->toBe([]);
        },
    );

    it('gets user info', function (): void {
        $claims = new DataSet(['sub' => 'mock-sub'], '');

        $token = Mockery::mock(UnencryptedToken::class);
        $token->expects('claims')->andReturn($claims);

        $sut = new FetchUserInfoForM2M();

        $result = $sut->fetch($token);

        expect($result->isValid)->toBeTrue();

        expect($result->sub)->toBe('mock-sub');

        expect($result->email)->toBe('mock-sub@m2m');

        expect($result->name)->toBe('mock-sub');

        expect($result->givenName)->toBe('mock-sub');

        expect($result->familyName)->toBe('mock-sub');

        expect($result->picture)->toBe('');

        expect($result->roles)->toBe([]);
    });
});
