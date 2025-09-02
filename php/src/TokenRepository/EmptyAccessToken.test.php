<?php

declare(strict_types=1);

use League\OAuth2\Client\Token\AccessTokenInterface;
use RxAnte\OAuth\TokenRepository\EmptyAccessToken;

describe('EmptyAccessToken', function (): void {
    uses()->group('EmptyAccessToken');

    it(
        'implements AccessTokenInterface',
        function (): void {
            $token = new EmptyAccessToken();

            expect($token)->toBeInstanceOf(
                AccessTokenInterface::class,
            );

            expect($token->getToken())->toBe('');

            expect($token->getRefreshToken())->toBeNull();

            expect($token->getExpires())->toBeNull();

            expect($token->hasExpired())->toBeTrue();

            expect($token->getValues())->toBe([]);

            expect((string) $token)->toBe('');

            expect($token->jsonSerialize())->toBe([]);
        },
    );
});
