<?php

declare(strict_types=1);

use RxAnte\OAuth\UserInfo\OauthUserInfo;

describe('OauthUserInfo', function (): void {
    it(
        'constructs with default values',
        function (): void {
            $userInfo = new OauthUserInfo();

            expect($userInfo->isValid)->toBeFalse();

            expect($userInfo->sub)->toBe('');

            expect($userInfo->email)->toBe('');

            expect($userInfo->name)->toBe('');

            expect($userInfo->givenName)->toBe('');

            expect($userInfo->familyName)->toBe('');

            expect($userInfo->picture)->toBe('');

            expect($userInfo->roles)->toBeArray()->toBeEmpty();
        },
    );

    it(
        'constructs with custom values',
        function (): void {
            $userInfo = new OauthUserInfo(
                isValid: true,
                sub: '123',
                email: 'test@example.com',
                name: 'Test User',
                givenName: 'Test',
                familyName: 'User',
                picture: 'pic.jpg',
                roles: ['admin', 'user'],
            );

            expect($userInfo->isValid)->toBeTrue();

            expect($userInfo->sub)->toBe('123');

            expect($userInfo->email)->toBe('test@example.com');

            expect($userInfo->name)->toBe('Test User');

            expect($userInfo->givenName)->toBe('Test');

            expect($userInfo->familyName)->toBe('User');

            expect($userInfo->picture)->toBe('pic.jpg');

            expect($userInfo->roles)->toBe(['admin', 'user']);
        },
    );

    it(
        'returns true if user has the role',
        function (): void {
            $userInfo = new OauthUserInfo(roles: ['admin', 'user']);

            expect($userInfo->hasRole('admin'))->toBeTrue();

            expect($userInfo->hasRole('user'))->toBeTrue();
        },
    );

    it(
        'returns false if user does not have the role',
        function (): void {
            $userInfo = new OauthUserInfo(roles: ['admin', 'user']);

            expect($userInfo->hasRole('guest'))->toBeFalse();
        },
    );

    it(
        'returns false if roles is empty',
        function (): void {
            $userInfo = new OauthUserInfo();

            expect($userInfo->hasRole('admin'))->toBeFalse();
        },
    );
});
