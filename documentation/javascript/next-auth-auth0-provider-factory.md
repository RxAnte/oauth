# NextAuthAuth0ProviderFactory

The `NextAuthAuth0ProviderFactory` takes a configuration object argument with a few items and builds a [NextAuth](https://next-auth.js.org) `Provider` with needed parameters to work with Auth0 and the RxAnte OAuth process.

See [NextAuthOptionsConfigFactory](next-auth-options-config-factory.md) for a code example.

## Config Argument Parameters

### `wellKnownUrl`: `string`

Provide the URL of the Auth0 Well Known endpoint. Example: https://myenv.us.auth0.com/.well-known/openid-configuration

### `clientId`: `string`

The Auth0 client ID to use for this provider.

### `clientSecret`: `string`

The Auth0 client secret to use for this provider.

### `audience`: `string`

Specify the api/audience to use for this provider.

### `id`: `string` : optional

The ID of this provider.

### `name`: `string` : optional

The Name of this provider
