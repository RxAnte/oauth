import { TokenRepository } from '../../TokenRepository/TokenRepository';
export default function RespondToAuthCodeCallback(tokenRepository: TokenRepository, request: Request, appUrl: string, tokenUrl: string, userInfoUrl: string, clientId: string, clientSecret: string, callbackUri?: string): Promise<Response>;
