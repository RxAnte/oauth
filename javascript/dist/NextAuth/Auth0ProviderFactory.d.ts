import { Provider } from 'next-auth/providers';
export declare function Auth0ProviderFactory({ wellKnownUrl, clientId, clientSecret, audience, id, name, }: {
    wellKnownUrl: string;
    clientId: string;
    clientSecret: string;
    audience: string;
    id?: string;
    name?: string;
}): Provider;
