import type { Provider } from 'next-auth/providers/index';
/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
export declare function Auth0ProviderFactory({ wellKnownUrl, clientId, clientSecret, audience, id, name, }: {
    wellKnownUrl: string;
    clientId: string;
    clientSecret: string;
    audience: string;
    id?: string;
    name?: string;
}): Provider;
