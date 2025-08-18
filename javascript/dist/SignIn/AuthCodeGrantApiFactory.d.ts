import { AuthCodeGrantApi } from './AuthCodeGrantApi';
import { TokenRepository } from '../TokenRepository/TokenRepository';
export declare function AuthCodeGrantApiFactory({ tokenRepository, appUrl, authorizeUrl, tokenUrl, userInfoUrl, clientId, clientSecret, callbackUri, audience, }: {
    tokenRepository: TokenRepository;
    appUrl: string;
    authorizeUrl: string;
    tokenUrl: string;
    userInfoUrl: string;
    clientId: string;
    clientSecret: string;
    callbackUri?: string;
    audience?: string;
}): AuthCodeGrantApi;
