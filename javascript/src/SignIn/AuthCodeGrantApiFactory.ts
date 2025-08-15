import { AuthCodeGrantApi } from './AuthCodeGrantApi';
import CreateSignInRouteResponse from './Internal/CreateSignInRouteResponse';

export function AuthCodeGrantApiFactory (
    {
        appUrl,
        authorizeUrl,
        clientId,
        callbackUri = '/api/auth/callback',
    }: {
        appUrl: string;
        authorizeUrl: string;
        clientId: string;
        callbackUri?: string;
    },
): AuthCodeGrantApi {
    return {
        createSignInRouteResponse: async (
            request: Request,
        ) => CreateSignInRouteResponse(
            request,
            appUrl,
            authorizeUrl,
            clientId,
            callbackUri,
        ),
    };
}
