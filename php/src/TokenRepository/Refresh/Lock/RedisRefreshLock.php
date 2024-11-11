<?php

/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository\Refresh\Lock;

use Redis;
use RuntimeException;

use function sleep;

// phpcs:disable Squiz.Arrays.ArrayDeclaration.NoKeySpecified

readonly class RedisRefreshLock implements RefreshLock
{
    public function __construct(private Redis $redis)
    {
    }

    public function acquire(string $accessToken): void
    {
        $tries = 0;

        $acquiredLock = false;

        do {
            $resp = $this->redis->set(
                'refresh_token_lock:' . $accessToken,
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

            sleep(1);
        } while ($tries < 65);

        if ($acquiredLock) {
            return;
        }

        throw new RuntimeException('Could not acquire lock');
    }

    public function release(string $accessToken): void
    {
        $this->redis->del('refresh_token_lock:' . $accessToken);
    }
}
