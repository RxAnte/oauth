# TokenRepository

[ioredis]: https://github.com/redis/ioredis

The `TokenRepository` type is required by the [`NextAuthOptionsConfigFactory`](next-auth-options-config-factory.md) and the [`RequestFactory`](request-factory.md). This package provides on based on [ioredis][ioredis] or you can provide your own implementation.

## `TokenRepositoryForIoRedisFactory`


```typescript
import { TokenRepositoryForIoRedisFactory } from 'rxante-oauth';
import getRedisClient from '../../cache/RedisClient';
import { ConfigOptions, getConfigStringServerSide } from '../../serverSideRunTimeConfig';

export function TokenRepositoryFactory () {
    return TokenRepositoryForIoRedisFactory({
        redis: getRedisClient(),
        secret: getConfigStringServerSide(ConfigOptions.NEXTAUTH_SECRET),
        redisTokenExpireTimeInSeconds: 4800,
    });
}
```

### Config Argument Parameters

### `redis`: `Redis` from the [ioredis][ioredis] package

### `secret`: `string`

### `redisTokenExpireTimeInSeconds`: `number`
