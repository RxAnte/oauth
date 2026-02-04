<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Psr\Log\LoggerInterface;
use RxAnte\OAuth\NoOpLogger;

use function implode;

readonly class UserInfoFetchNoOp implements UserInfoFetchLock
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface|null $logger)
    {
        $this->logger = $logger ?? new NoOpLogger();
    }

    public function acquire(string $key): void
    {
        $this->logger->warning(implode('', [
            UserInfoFetchLock::class,
            '::acquire called but no implementation provided',
        ]));
    }

    public function release(string $key): void
    {
        $this->logger->warning(implode('', [
            UserInfoFetchLock::class,
            '::release called but no implementation provided',
        ]));
    }
}
