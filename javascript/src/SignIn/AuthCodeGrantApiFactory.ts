import { AuthCodeGrantApi } from './AuthCodeGrantApi';
import CreateSignInRouteResponse from './Internal/CreateSignInRouteResponse';
import RespondToAuthCodeCallback, { TokenResponseJson, UserInfoJson } from './Internal/RespondToAuthCodeCallback';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { TokenData } from '../TokenData';
import DeleteSessionAndCookie from './Internal/DeleteSessionAndCookie';

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
            onBeforeSuccessRedirect: (params: {
                sessionId: string;
                token: TokenData;
                userInfoJson: UserInfoJson;
                tokenJson: TokenResponseJson;
            }) => void = () => {},
        ) => RespondToAuthCodeCallback(
            tokenRepository,
            request,
            appUrl,
            tokenUrl,
            userInfoUrl,
            clientId,
            clientSecret,
            callbackUri,
            onBeforeSuccessRedirect,
        ),
        deleteSessionAndCookie: async () => DeleteSessionAndCookie(
            tokenRepository,
        ),
    };
}
