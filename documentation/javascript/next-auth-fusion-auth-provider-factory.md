# NextAuthFusionAuthProviderFactory

The `NextAuthFusionAuthProviderFactory` takes a configuration object argument with a few items and builds a [NextAuth](https://next-auth.js.org) `Provider` with needed parameters to work with FusionAuth and the RxAnte OAuth process.

See [NextAuthOptionsConfigFactory](next-auth-options-config-factory.md) for a code example.

## Config Argument Parameters

### `wellKnownUrl`: `string`

Provide the URL of the FusionAuth Well Known endpoint. Example: https://myenv.us.auth0.com/.well-known/openid-configuration

### `clientId`: `string`

The FusionAuth client ID to use for this provider.

### `clientSecret`: `string`

The FusionAuth client secret to use for this provider.

### `id`: `string` (optional)

The ID of this provider.

### `name`: `string` (optional)

The Name of this provider
