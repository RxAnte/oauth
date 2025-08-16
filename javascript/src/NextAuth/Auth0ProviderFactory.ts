import { Provider } from 'next-auth/providers';

/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
export function Auth0ProviderFactory (
    {
        wellKnownUrl,
        clientId,
        clientSecret,
        audience,
        id = 'auth0',
        name = 'Auth0',
    }: {
        wellKnownUrl: string;
        clientId: string;
        clientSecret: string;
        audience: string;
        id?: string;
        name?: string;
    },
): Provider {
    return {
        wellKnown: wellKnownUrl,
        clientId,
        clientSecret,
        id,
        name,
        type: 'oauth',
        checks: ['state'],
        authorization: {
            params: {
                scope: 'openid profile email offline_access', // offline_access required for refresh tokens :/
                audience,
            },
        },
        httpOptions: {
            timeout: 30000,
        },
        userinfo: {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-ignore
            async request ({ client, tokens }) {
                // Get base profile
                // noinspection UnnecessaryLocalVariableJS
                const profile = await client.userinfo(
                    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
                    // @ts-ignore
                    tokens,
                );

                return profile;
            },
        },
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-ignore
        profile (profile) {
            return {
                id: profile.sub,
                ...profile,
            };
        },
    };
}
