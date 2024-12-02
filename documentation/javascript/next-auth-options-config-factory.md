## NextAuthOptionsConfigFactory

The `NextAuthOptionsConfigFactory` takes a configuration object argument with a few items and builds a [NextAuth](https://next-auth.js.org) `AuthOptions` object with the needed parameters to make the RxAnte OAuth process work.

To use it, create a [Next](https://nextjs.org) route file at the following location:

`app/api/auth/[...nextauth]/route.ts`

Here is an example of what that file might look like.

(This example shows using the `NextAuthAuth0ProviderFactory` which you can read more about [here](next-auth-auth0-provider-factory.md))

```typescript
import NextAuth from 'next-auth';
import {
    NextAuthOptionsConfigFactory,
    NextAuthAuth0ProviderFactory,
    TokenRepositoryForIoRedisFactory,
} from 'rxante-oauth';
import {
    ConfigOptions,
    getConfigBooleanServerSide,
    getConfigStringServerSide,
} from '../../../serverSideRunTimeConfig';
import getRedisClient from '../../../cache/RedisClient';

const handler = NextAuth(NextAuthOptionsConfigFactory({
    debug: getConfigBooleanServerSide(ConfigOptions.DEV_MODE),
    providers: [NextAuthAuth0ProviderFactory({
        wellKnownUrl: getConfigStringServerSide(
            ConfigOptions.NEXTAUTH_WELL_KNOWN_URL,
        ),
        clientId: getConfigStringServerSide(
            ConfigOptions.NEXTAUTH_CLIENT_ID,
        ),
        clientSecret: getConfigStringServerSide(
            ConfigOptions.NEXTAUTH_CLIENT_SECRET,
        ),
        audience: 'example-audience',
    })],
    secret: getConfigStringServerSide(
        ConfigOptions.NEXTAUTH_SECRET,
    ),
    tokenRepository: TokenRepositoryForIoRedisFactory({
        redis: getRedisClient(),
        redisTokenExpireTimeInSeconds: 4800,
    }),
}));

export { handler as GET, handler as POST };
```

## Config Argument Parameters

### `secret`: `string`

A secret key that NextAuth wil use.

### `providers`: `Array<Provider>`

An array of NextAuth `Provider`s.

### `tokenRepository`: `TokenRepository`

Provide an implementation of `TokenRepository`

### `debug`: `boolean`

Whether NextAuth should be in debug mode or not.
