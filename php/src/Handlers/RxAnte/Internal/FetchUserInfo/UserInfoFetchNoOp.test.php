<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\UserInfoFetchLock;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\UserInfoFetchNoOp;

describe('UserInfoFetchNoOp', function (): void {
    uses()->group('UserInfoFetchNoOp');

    it(
        'logs warning when acquire is called',
        function (): void {
            $logger = Mockery::mock(LoggerInterface::class);
            $logger->expects('warning')
                ->with(UserInfoFetchLock::class . '::acquire called but no implementation provided')
                ->once();

            $sut = new UserInfoFetchNoOp(logger: $logger);

            $sut->acquire('test-key');
        },
    );

    it(
        'logs warning when release is called',
        function (): void {
            $logger = Mockery::mock(LoggerInterface::class);
            $logger->expects('warning')
                ->with(UserInfoFetchLock::class . '::release called but no implementation provided')
                ->once();

            $sut = new UserInfoFetchNoOp(logger: $logger);

            $sut->release('test-key');
        },
    );
});
