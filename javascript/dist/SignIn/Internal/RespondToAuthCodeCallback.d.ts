import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { TokenData } from '../../TokenData';
export type TokenResponseJson = {
    token_type: string;
    expires_in: number;
    access_token: string;
    refresh_token: string;
};
export type UserInfoJson = {
    sub: string;
    email: string;
    name: string;
    given_name: string;
    family_name: string;
};
export default function RespondToAuthCodeCallback(tokenRepository: TokenRepository, request: Request, appUrl: string, tokenUrl: string, userInfoUrl: string, clientId: string, clientSecret: string, callbackUri?: string, onBeforeSuccessRedirect?: (params: {
    sessionId: string;
    token: TokenData;
    userInfoJson: UserInfoJson;
    tokenJson: TokenResponseJson;
}) => void): Promise<Response>;
