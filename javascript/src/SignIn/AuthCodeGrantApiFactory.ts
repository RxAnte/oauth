import { AuthCodeGrantApi } from './AuthCodeGrantApi';
import CreateSignInRouteResponse from './Internal/CreateSignInRouteResponse';
import RespondToAuthCodeCallback from './Internal/RespondToAuthCodeCallback';
import { TokenRepository } from '../TokenRepository/TokenRepository';

export function AuthCodeGrantApiFactory (
    {
        tokenRepository,
        appUrl,
        authorizeUrl,
        tokenUrl,
        userInfoUrl,
        clientId,
        clientSecret,
        callbackUri = '/api/auth/callback',
        audience,
    }: {
        tokenRepository: TokenRepository;
        appUrl: string;
        authorizeUrl: string;
        tokenUrl: string;
        userInfoUrl: string;
        clientId: string;
        clientSecret: string;
        callbackUri?: string;
        audience?: string;
    },
): AuthCodeGrantApi {
    return {
        createSignInRouteResponse: async (
            request: Request,
            modifyAuthorizeUrl = () => {},
        ) => CreateSignInRouteResponse(
            request,
            appUrl,
            authorizeUrl,
            clientId,
            callbackUri,
            audience,
            modifyAuthorizeUrl,
        ),
        respondToAuthCodeCallback: async (
            request: Request,
        ) => RespondToAuthCodeCallback(
            tokenRepository,
            request,
            appUrl,
            tokenUrl,
            userInfoUrl,
            clientId,
            clientSecret,
            callbackUri,
        ),
    };
}
