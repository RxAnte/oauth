# RequestFactory

[TokenRepository]: token-repository.md
[RefreshAccessToken]: refresh-access-token.md

The RequestFactory is used to create a `Request` type used for making `HTTP` requests.

The [Middleware](middleware.md) must be in place for `makeWithSignInRedirect` to function correctly.

In your own application, you would probably create your own `RequestFactory` as a wrapper around this package's request factory, something like this:

```typescript
import {
    RequestFactory as BaseRequestFactory,
    RefreshAccessTokenWithAuth0Factory,
    IoRedisRefreshLockFactory,
} from 'rxante-oauth';
import {
    ConfigOptions,
    getConfigStringServerSide,
} from '../../serverSideRunTimeConfig';
import { TokenRepositoryFactory } from '../auth/TokenRepositoryFactory';
import getRedisClient from '../../cache/RedisClient';

export function RequestFactory () {
    const tokenRepository = TokenRepositoryFactory();

    return BaseRequestFactory({
        appUrl: getConfigStringServerSide(ConfigOptions.APP_URL),
        requestBaseUrl: getConfigStringServerSide(ConfigOptions.API_BASE_URL),
        tokenRepository,
        nextAuthProviderId: 'auth0',
        refreshAccessToken: RefreshAccessTokenWithAuth0Factory({
            tokenRepository,
            refreshLock: IoRedisRefreshLockFactory({redis: getRedisClient()}),
            wellKnownUrl: getConfigStringServerSide(
                ConfigOptions.NEXTAUTH_WELL_KNOWN,
            ),
            clientId: getConfigStringServerSide(
                ConfigOptions.NEXTAUTH_CLIENT_ID,
            ),
            clientSecret: getConfigStringServerSide(
                ConfigOptions.NEXTAUTH_CLIENT_SECRET,
            ),
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

### `nextAuthProviderId`: string

The ID of the provider you are using.

### `refreshAccessToken`: [`RefreshAccessToken`][RefreshAccessToken]

An implementation of [`RefreshAccessToken`][RefreshAccessToken] is required to refresh the access token when required.

## `Request` Type

The `Request` type then has the following methods:

## `makeWithoutToken`

Makes an HTTP request with all the same input parameters as the other methods, but without token authentication.

## `makeWithToken`

Makes a token authenticated HTTP request. If there is no token or the token is invalid, this method does nothing about that. It would be up to you to do something with that information.

## `makeWithSignInRedirect`

Makes a token authenticated HTTP request, and if there's no token or the token is invalid, it will redirect to the sign-in oauth flow.
