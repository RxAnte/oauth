# AuthCodeGrantApi

[TokenRepository]: token-repository.md
[ioredis]: https://github.com/redis/ioredis

The `AuthCodeGrantApi` type is used to initiate an oauth request and respond to the callback to acquire tokens. There are two ways to create an implementation of this type: `AuthCodeGrantApiFactory` in which you directly provide all the parameters the factory needs, or the `WellKnownAuthCodeGrantApiFactory` which ascertains the endpoint parameters from the auth server's well-known open id configuration URL. The latter is recommended.

## `WellKnownAuthCodeGrantApiFactory`

In your own application, you would probably create your own `RequestFactory` as a wrapper around this package's request factory, something like this:

```typescript
import { WellKnownAuthCodeGrantApiFactory } from 'rxante-oauth';
import { TokenRepositoryFactory } from './TokenRepositoryFactory';
import getRedisClient from './RedisClient';

export async function AuthCodeGrantApiFactory () {
    return WellKnownAuthCodeGrantApiFactory({
        tokenRepository: TokenRepositoryFactory(),
        appUrl: 'https://APP_URL_HERE.tld',
        wellKnownUrl: 'https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration',
        clientId: 'CLIENT_ID_HERE',
        clientSecret: 'CLIENT_SECRET_HERE',
        callbackUri: '/auth/callback', // Optional. Default is /api/auth/callback
        redis: getRedisClient(),
    });
}
```

### `tokenRepository`: [`TokenRepository`][TokenRepository]

An implementation of the [`TokenRepository`][TokenRepository] is required for the token refresh process.

### `appUrl`: `string`

This is required for the oauth flow to redirect as the oauth flow requires redirecting back to the application after the oauth server has authenticated the user.

### `wellKnownUrl`: `string`

Provide the URL of the Well Known endpoint. Example: https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration

### `clientId`: `string`

The client ID to use for the specified provider.

### `clientSecret`: `string`

The client secret to use for the specified provider.

### `callbackUri`: `string`

Optional. Default is `/api/auth/callback`

### `redis`: `Redis` from the [ioredis][ioredis] package

Optional, but recommended. Used for caching and retrieving well-known open id configuration

### `wellKnownCacheKey`: `string`

Optional. Defaults to `rxante_oauth_well_known`.

### `wellKnownCacheExpiresInSeconds`: `number`

Optional. Defaults to `86400` (24 hours)

## Routing

You must create two routes and use the `AuthCodeGrantApi` to respond to those route requests.

### Sign In Route

If you are creating a route at `/auth/sign-in` you would create a file called `app/auth/sign-in/route.ts` as follows:

```typescript
import { AuthCodeGrantApiFactory } from './AuthCodeGrantApiFactory';

export async function GET (request: Request) {
    return (await AuthCodeGrantApiFactory()).createSignInRouteResponse(request);
}
```

### Callback Route

If you are creating a route at `/auth/callback` you would create a file called `app/auth/callback/route.ts` as follows:

```typescript
import { AuthCodeGrantApiFactory } from './AuthCodeGrantApiFactory';

export async function GET (request: Request) {
    return (await AuthCodeGrantApiFactory()).respondToAuthCodeCallback(request);
}
```
