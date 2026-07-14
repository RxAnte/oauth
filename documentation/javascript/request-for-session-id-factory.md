# RequestForSessionIdFactory

[TokenRepository]: token-repository.md
[RefreshAccessTokenForSessionId]: #refreshaccesstokenforsessionidfactory

The `RequestForSessionIdFactory` is a session-id-driven counterpart to the [RequestFactory](request-factory.md) for use in the Next.js Pages Router,  specifically `getServerSideProps`. It exists because `next/headers` `cookies()` throws when called from the Pages Router, which makes the cookie-based `RequestFactory` unusable there.

Instead of reading the session id from the request cookies via `next/headers`, this path takes the session id as an explicit argument. It is backed by the same Redis-based token store, refresh, and lock as the App Router path — it never touches cookies, and refreshing a token persists the new token to Redis under the same session id without rotating the `oauthSessionId` cookie.

There is intentionally no `makeWithoutToken` or `makeWithSignInRedirect` on this path. A Pages Router caller issues its own redirect via `getServerSideProps`'s `{ redirect }` return, so there's no need for `next/navigation` here.

## `SessionIdFromPagesRequest`

Reads the `oauthSessionId` cookie value out of a Pages Router request object (`context.req` in `getServerSideProps`, or a Pages API route's `req`).

```typescript
import { SessionIdFromPagesRequest } from 'rxante-oauth';

const sessionId = SessionIdFromPagesRequest(context.req);
```

Returns the cookie value, or `null` if it isn't present.

## `OAUTH_SESSION_ID_COOKIE`

The cookie name (`'oauthSessionId'`) used to store the session id. Exported  so consumers don't need to hardcode it.

## `RefreshAccessTokenForSessionIdFactory`

The session-id counterpart to [`RefreshAccessTokenFactory`](refresh-access-token.md). Behaves identically,
except it reads/writes the token in Redis by session id (`findTokenBySessionId` / `setTokenFromSessionId`) rather than through
cookies. If no token is found for the session id, it returns without attempting a refresh.

### Config Argument Parameters

Same as [`RefreshAccessTokenFactory`](refresh-access-token.md#config-argument-parameters): `tokenRepository`, `refreshLock`, `wellKnownUrl`, `clientId`, `clientSecret`, `redis` (optional), `wellKnownCacheKey` (optional), and `wellKnownCacheExpiresInSeconds` (optional).

## `RequestForSessionIdFactory`

```typescript
import {
    RequestForSessionIdFactory,
    RefreshAccessTokenForSessionIdFactory,
    IoRedisRefreshLockFactory,
} from 'rxante-oauth';
import { TokenRepositoryFactory } from './TokenRepositoryFactory';
import getRedisClient from './RedisClient';

export function RequestForSessionId () {
    const tokenRepository = TokenRepositoryFactory();

    return RequestForSessionIdFactory({
        requestBaseUrl: 'https://RESOURCE_SERVER_URL_HERE.tld',
        tokenRepository,
        refreshAccessToken: RefreshAccessTokenForSessionIdFactory({
            tokenRepository,
            refreshLock: IoRedisRefreshLockFactory({redis: getRedisClient()}),
            wellKnownUrl: 'https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration',
            clientId: 'CLIENT_ID_HERE',
            clientSecret: 'CLIENT_SECRET_HERE',
            redis: getRedisClient(),
        }),
    });
}
```

### Config Argument Parameters

#### `requestBaseUrl`: `string`

The base URL for the HTTP requests.

#### `tokenRepository`: [`TokenRepository`][TokenRepository]

An implementation of the [`TokenRepository`][TokenRepository] is required for the token refresh process.

#### `nextAuthProviderId`: string (deprecated, not needed when not using next-auth)

The ID of the provider you are using. Not needed unless you have legacy next-auth `Provider` header requirements to carry forward.

#### `refreshAccessToken`: [`RefreshAccessTokenForSessionId`][RefreshAccessTokenForSessionId]

A function returned from [`RefreshAccessTokenForSessionIdFactory`](#refreshaccesstokenforsessionidfactory).

## `RequestForSessionId` Type

### `makeWithToken`

Makes a token authenticated HTTP request for the given session id. If there  is no token or the token is invalid, this method does nothing about that. It  would be up to you to do something with that information (e.g. redirect to sign-in on a `401`).

```typescript
const response = await request.makeWithToken(sessionId, {
    uri: '/some-backend-path',
});
```

## Request Method Parameters

The `sessionId` is passed as the first argument. The second argument accepts  the same optional properties object as [`RequestFactory`](request-factory.md#request-method-parameters): `uri`, `method`, `queryParams`, `payload`, `cacheTags`, `cacheSeconds`, and `headers`.

## Usage in `getServerSideProps`

```typescript
export async function getServerSideProps (context) {
    const sessionId = SessionIdFromPagesRequest(context.req);

    if (!sessionId) {
        return {
            redirect: {
                destination: '/api/auth/sign-in',
                permanent: false,
            },
        };
    }

    const request = RequestForSessionIdFactory({
        requestBaseUrl: 'https://RESOURCE_SERVER_URL_HERE.tld',
        tokenRepository,
        refreshAccessToken: RefreshAccessTokenForSessionIdFactory({
            tokenRepository,
            refreshLock: IoRedisRefreshLockFactory({ redis }),
            wellKnownUrl,
            clientId,
            clientSecret,
            redis,
        }),
    });

    const response = await request.makeWithToken(sessionId, {
        uri: '/some-backend-path',
    });

    if (response.status === 401) {
        return {
            redirect: {
                destination: '/api/auth/sign-in',
                permanent: false,
            },
        };
    }

    return {
        props: {
            data: response.json,
        },
    };
}
```
