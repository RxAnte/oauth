// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import sleep from 'sleep-promise';
import { RefreshLock } from './RefreshLock';

export function IoRedisRefreshLockFactory (
    {
        redis,
    }: {
        redis: Redis;
    },
): RefreshLock {
    return {
        acquire: async (accessToken: string) => {
            let resp = null;

            let tries = 0;

            let acquiredLock = false;

            do {
                // eslint-disable-next-line no-await-in-loop
                resp = await redis.set(
                    `refresh_token_lock:${accessToken}`,
                    'true',
                    'EX',
                    60,
                    'NX',
                );

                if (resp !== null && resp.toLowerCase() === 'ok') {
                    acquiredLock = true;

                    break;
                }

                tries += 1;

                // eslint-disable-next-line no-await-in-loop
                await sleep(1000);
            } while (tries < 65);

            if (acquiredLock) {
                return;
            }

            throw new Error('Could not acquire lock');
        },
        release: async (accessToken: string) => {
            await redis.del(`refresh_token_lock:${accessToken}`);
        },
    };
}
