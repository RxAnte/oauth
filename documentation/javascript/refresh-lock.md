# RefreshLock

[ioredis]: https://github.com/redis/ioredis

The `RefreshLock` type is used to make sure only one processes or request is refreshing the AccessToken at one time. This package provides an [ioredis][ioredis] implementation.

## `IoRedisRefreshLockFactory`

Implements the `RefreshLock` for [ioredis][ioredis]. For a code sample see the documentation for [RequestFactory](request-factory.md).

### Config Argument Parameters

### `redis`: `Redis` from the [ioredis][ioredis] package
