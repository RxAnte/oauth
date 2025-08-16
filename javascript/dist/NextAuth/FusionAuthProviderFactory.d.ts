import { Provider } from 'next-auth/providers';
/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
export declare function FusionAuthProviderFactory({ wellKnownUrl, clientId, clientSecret, id, name, }: {
    wellKnownUrl: string;
    clientId: string;
    clientSecret: string;
    id?: string;
    name?: string;
}): Provider;
