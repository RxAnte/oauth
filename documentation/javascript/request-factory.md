# RequestFactory

[TokenRepository]: token-repository.md
[RefreshAccessToken]: refresh-access-token.md

The RequestFactory is used to create a `Request` api instance used for making `HTTP` requests.

The [Next Middleware Headers Factory](next-middleware-headers-factory.md) must be in place for `makeWithSignInRedirect` to function correctly.

In your own application, you would probably create your own `RequestFactory` as a wrapper around this package's request factory, something like this:

```typescript
import {
    RequestFactory as BaseRequestFactory,
    RefreshAccessTokenFactory,
    IoRedisRefreshLockFactory,
} from 'rxante-oauth';
import { TokenRepositoryFactory } from './TokenRepositoryFactory';
import getRedisClient from './RedisClient';

export function RequestFactory () {
    const tokenRepository = TokenRepositoryFactory();

    return BaseRequestFactory({
        appUrl: 'https://APP_URL_HERE.tld',
        requestBaseUrl: 'https://RESOURCE_SERVER_URL_HERE.tld',
        tokenRepository,
        refreshAccessToken: RefreshAccessTokenFactory({
            tokenRepository,
            refreshLock: IoRedisRefreshLockFactory({redis: getRedisClient()}),
            wellKnownUrl: 'https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration',
            clientId: 'CLIENT_ID_HERE',
            clientSecret: 'CLIENT_SECRET_HERE',
            redis: getRedisClient(), // Optional, but recommended. Used for caching and retrieving well-known open id configuration
        }),
    });
}
```

## Config Argument Parameters

### `appUrl`: `string`

This is required for the sign-in redirect to work properly as the oauth flow requires redirecting back to the application after the oauth server has authenticated the user.

### `requestBaseUrl`: `string`

The base URL for the HTTP requests.

### `tokenRepository`: [`TokenRepository`][TokenRepository]

An implementation of the [`TokenRepository`][TokenRepository] is required for the token refresh process.

### `nextAuthProviderId`: string (deprecated, not needed when not using next-auth)

The ID of the provider you are using. Not needed in 2.0.

### `refreshAccessToken`: [`RefreshAccessToken`][RefreshAccessToken]

An implementation of [`RefreshAccessToken`][RefreshAccessToken] is required to refresh the access token when required.

### `signInUri`: `string`

Optional. Defaults to `/api/auth/sign-in` for historical reasons having to do with next-auth. When setting up the [`AuthCodeGrantApi`](auth-code-grant-api.md) you can change this to any route you like, which must be relfected here if you do so.

## `Request` Type

The `Request` type then has the following methods:

## `makeWithoutToken`

Makes an HTTP request with all the same input parameters as the other methods, but without token authentication.

## `makeWithToken`

Makes a token authenticated HTTP request. If there is no token or the token is invalid, this method does nothing about that. It would be up to you to do something with that information.

## `makeWithSignInRedirect`

Makes a token authenticated HTTP request, and if there's no token or the token is invalid, it will redirect to the sign-in oauth flow.
