<?php

/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace RxAnte\OAuth\TokenRepository\Refresh\Lock;

use Closure;
use Redis;
use RuntimeException;

use function sleep;

// phpcs:disable Squiz.Arrays.ArrayDeclaration.NoKeySpecified

readonly class RedisRefreshLock implements RefreshLock
{
    private Closure $sleep;

    public function __construct(
        private Redis $redis,
        Closure|null $sleep = null,
    ) {
        $sleep ??= static fn (int $seconds) => sleep($seconds);

        $this->sleep = $sleep;
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

            ($this->sleep)(1);
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
