# RefreshAccessToken

[TokenRepository]: token-repository.md
[RefreshLock]: refresh-lock.md

`RefreshAccessToken` is a async function type that refreshes the access token. The one provided with this package works with [Auth0](https://auth0.com).

## `RefreshAccessTokenWithAuth0Factory`

The `RefreshAccessTokenWithAuth0Factory` returns a function that will refresh the access token for the Auth0 provider. For a code sample of implementation, see the [RequestFactory](request-factory.md) documentation.

## Config Argument Parameters

### `tokenRepository`: [`TokenRepository`][TokenRepository]

An implementation of the [`TokenRepository`][TokenRepository] is required.

### `refreshLock`: [`RefreshLock`][RefreshLock]

An implementation of the [`RefreshLock`][RefreshLock] is required.

### `wellKnownUrl`: `string`

Provide the URL of the Auth0 Well Known endpoint. Example: https://myenv.us.auth0.com/.well-known/openid-configuration

### `clientId`: `string`

The Auth0 client ID to use for the specified provider.

### `clientSecret`: `string`

The Auth0 client secret to use for the specified provider.
