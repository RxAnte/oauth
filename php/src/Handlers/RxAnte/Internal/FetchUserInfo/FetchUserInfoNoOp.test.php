<?php

declare(strict_types=1);

use Lcobucci\JWT\UnencryptedToken;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\FetchUserInfoNoOp;

describe('FetchUserInfoNoOp', function (): void {
    uses()->group('FetchUserInfoNoOp');

    it('returns empty user info', function (): void {
        $sut = new FetchUserInfoNoOp();

        $result = $sut->fetch(
            Mockery::mock(UnencryptedToken::class),
        );

        expect($result->isValid)->toBeFalse();
    });
});
