<?php

declare(strict_types=1);

use RxAnte\OAuth\UserInfo\Jwt;

describe('Jwt', function (): void {
    uses()->group('Jwt');

    it('can be instantiated with default values', function (): void {
        $jwt = new Jwt();

        expect($jwt->rawToken)->toBe('');

        expect($jwt->aud)->toBe('');

        expect($jwt->jti)->toBe('');

        expect($jwt->iat)->toBe(0);

        expect($jwt->nbf)->toBe(0);

        expect($jwt->exp)->toBe(0);

        expect($jwt->sub)->toBe('');

        expect($jwt->scopes)->toBeArray()->toBeEmpty();

        expect($jwt->cacheKey)->toBe(
            'user_info_token_' . md5(''),
        );
    });

    it('sets all properties and cacheKey correctly', function (): void {
        $jwt = new Jwt(
            'token123',
            'audience',
            'jwtid',
            1234567890,
            1234567800,
            1234567999,
            'subject',
            ['read', 'write'],
        );

        expect($jwt->rawToken)->toBe('token123');

        expect($jwt->aud)->toBe('audience');

        expect($jwt->jti)->toBe('jwtid');

        expect($jwt->iat)->toBe(1234567890);

        expect($jwt->nbf)->toBe(1234567800);

        expect($jwt->exp)->toBe(1234567999);

        expect($jwt->sub)->toBe('subject');

        expect($jwt->scopes)->toBe(['read', 'write']);

        expect($jwt->cacheKey)->toBe(
            'user_info_token_' . md5('token123'),
        );
    });
});
