<?php

declare(strict_types=1);

use RxAnte\OAuth\CustomAuthenticationHook;
use RxAnte\OAuth\CustomAuthenticationHookFactory;
use RxAnte\OAuth\CustomAuthenticationHookNoOp;

describe('CustomAuthenticationHookFactory', function (): void {
    uses()->group('CustomAuthenticationHookFactory');

    it(
        'returns the provided CustomAuthenticationHook',
        function (): void {
            $mockHook = Mockery::mock(CustomAuthenticationHook::class);

            $factory = new CustomAuthenticationHookFactory(
                $mockHook,
            );

            $result = $factory->create();

            expect($result)->toBe($mockHook);
        },
    );

    it(
        'returns CustomAuthenticationHookNoOp if no hook is provided',
        function (): void {
            $factory = new CustomAuthenticationHookFactory();

            $result = $factory->create();

            expect($result)->toBeInstanceOf(
                CustomAuthenticationHookNoOp::class,
            );
        },
    );
});
