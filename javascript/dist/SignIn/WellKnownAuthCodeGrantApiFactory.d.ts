import { AuthCodeGrantApi } from './AuthCodeGrantApi';
import { TokenRepository } from '../TokenRepository/TokenRepository';
export declare function WellKnownAuthCodeGrantApiFactory({ tokenRepository, appUrl, wellKnownUrl, clientId, clientSecret, callbackUri, }: {
    tokenRepository: TokenRepository;
    appUrl: string;
    wellKnownUrl: string;
    clientId: string;
    clientSecret: string;
    callbackUri?: string;
}): Promise<AuthCodeGrantApi>;
