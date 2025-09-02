<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\CustomAuthenticationHookNoOp;
use RxAnte\OAuth\CustomAuthenticationResult;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

describe('CustomAuthenticationHookNoOp', function (): void {
    uses()->group('CustomAuthenticationHookNoOp');

    it(
        'returns a CustomAuthenticationResult from process',
        function (): void {
            $userInfo = Mockery::mock(OauthUserInfo::class);

            $request = Mockery::mock(ServerRequestInterface::class);

            $response = Mockery::mock(ResponseInterface::class);

            $hook = new CustomAuthenticationHookNoOp();

            $result = $hook->process(
                $userInfo,
                $request,
                $response,
            );

            expect($result)->toBeInstanceOf(CustomAuthenticationResult::class);
        },
    );
});
