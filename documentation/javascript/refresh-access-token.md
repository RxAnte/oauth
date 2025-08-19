# RefreshAccessToken

[TokenRepository]: token-repository.md
[RefreshLock]: refresh-lock.md
[ioredis]: https://github.com/redis/ioredis

`RefreshAccessToken` is an async function type that refreshes the access token. An implementation is provided with this package.

## `RefreshAccessTokenFactory`

The `RefreshAccessTokenFactory` returns a function that will refresh the access token. For a code sample of implementation, see the [RequestFactory](request-factory.md) documentation.

## Config Argument Parameters

### `tokenRepository`: [`TokenRepository`][TokenRepository]

An implementation of the [`TokenRepository`][TokenRepository] is required.

### `refreshLock`: [`RefreshLock`][RefreshLock]

An implementation of the [`RefreshLock`][RefreshLock] is required.

### `wellKnownUrl`: `string`

Provide the URL of the Well Known endpoint. Example: https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration

### `clientId`: `string`

The client ID to use for the specified provider.

### `clientSecret`: `string`

The client secret to use for the specified provider.

### `redis`: `Redis` from the [ioredis][ioredis] package

Optional, but recommended. Used for caching and retrieving well-known open id configuration

### `wellKnownCacheKey`: `string`

Optional. Defaults to `rxante_oauth_well_known`.

### `wellKnownCacheExpiresInSeconds`: `number`

Optional. Defaults to `86400` (24 hours)
