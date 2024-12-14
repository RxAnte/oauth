import { Provider } from 'next-auth/providers';

export function FusionAuthProviderFactory (
    {
        wellKnownUrl,
        clientId,
        clientSecret,
        id = 'fusion-auth',
        name = 'FusionAuth',
    }: {
        wellKnownUrl: string;
        clientId: string;
        clientSecret: string;
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
            params: { scope: 'openid profile email offline_access' }, // offline_access required for refresh tokens :/
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
            if (!profile.name) {
                profile.name = `${profile.given_name} ${profile.family_name}`;
            }

            return {
                id: profile.sub,
                ...profile,
            };
        },
    };
}
