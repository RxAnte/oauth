import { Provider } from 'next-auth/providers';
export declare function FusionAuthProviderFactory({ wellKnownUrl, clientId, clientSecret, id, name, }: {
    wellKnownUrl: string;
    clientId: string;
    clientSecret: string;
    id?: string;
    name?: string;
}): Provider;
