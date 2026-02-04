<?php

/** @noinspection PhpComposerExtensionStubsInspection */
// phpcs:disable Squiz.Arrays.ArrayDeclaration.NoKeySpecified


declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Closure;
use Redis;
use RuntimeException;

use function sleep;

readonly class RedisUserInfoFetchLock implements UserInfoFetchLock
{
    private Closure $sleep;

    public function __construct(
        private Redis $redis,
        Closure|null $sleep = null,
    ) {
        $sleep ??= static fn (int $seconds) => sleep($seconds);

        $this->sleep = $sleep;
    }

    public function acquire(string $key): void
    {
        $tries = 0;

        $acquiredLock = false;

        do {
            $resp = $this->redis->set(
                'fetch_user_info_lock:' . $key,
                'true',
                [
                    'EX' => 60,
                    'NX',
                ],
            );

            if ($resp === true) {
                $acquiredLock = true;

                break;
            }

            $tries += 1;

            ($this->sleep)(1);
        } while ($tries < 65);

        if ($acquiredLock) {
            return;
        }

        throw new RuntimeException('Could not acquire lock');
    }

    public function release(string $key): void
    {
        $this->redis->del('fetch_user_info_from_auth0_lock:' . $key);
    }
}
