import { AuthCodeGrantApi } from './AuthCodeGrantApi';
export declare function AuthCodeGrantApiFactory({ appUrl, authorizeUrl, clientId, callbackUri, }: {
    appUrl: string;
    authorizeUrl: string;
    clientId: string;
    callbackUri?: string;
}): AuthCodeGrantApi;
