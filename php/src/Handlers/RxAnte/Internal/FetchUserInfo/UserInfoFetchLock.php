<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

interface UserInfoFetchLock
{
    public function acquire(string $key): void;

    public function release(string $key): void;
}
