# TokenRepository

[ioredis]: https://github.com/redis/ioredis

The `TokenRepository` is required for signing in, making authenticated HTTP requests, and refreshing the token. This package provides an implementation based on [ioredis][ioredis] or you can provide your own implementation.

## `TokenRepositoryForIoRedisFactory`

```typescript
import { TokenRepositoryForIoRedisFactory } from 'rxante-oauth';
import getRedisClient from './RedisClient';

export function TokenRepositoryFactory () {
    return TokenRepositoryForIoRedisFactory({
        redis: getRedisClient(),
        redisTokenExpireTimeInSeconds: 4800,
    });
}
```

### Config Argument Parameters

### `redis`: `Redis` from the [ioredis][ioredis] package

### `secret`: `string` (deprecated, not needed when not using next-auth)

### `redisTokenExpireTimeInSeconds`: `number`
