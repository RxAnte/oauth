<?php

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository\Refresh\Lock;

interface RefreshLock
{
    public function acquire(string $accessToken): void;

    public function release(string $accessToken): void;
}
