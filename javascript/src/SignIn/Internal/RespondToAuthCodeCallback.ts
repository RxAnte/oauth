import { cookies } from 'next/headers';
import { randomBytes } from 'crypto';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';

type ErrorJson = {
    error?: string;
    error_description?: string;
};

type TokenResponseJson = {
    token_type: string;
    expires_in: number;
    access_token: string;
    refresh_token: string;
};

type UserInfoJson = {
    sub: string;
    email: string;
    name: string;
    given_name: string;
    family_name: string;
};

// TODO: Refactor this to be less procedural
export default async function RespondToAuthCodeCallback (
    tokenRepository: TokenRepository,
    request: Request,
    appUrl: string,
    tokenUrl: string,
    userInfoUrl: string,
    clientId: string,
    clientSecret: string,
    callbackUri: string = '/api/auth/callback',
): Promise<Response> {
    const cookieStore = await cookies();

    // TODO: Validate that this URL domain matches the appUrl
    const authReturnCookie = cookieStore.get('authReturn');

    const authReturn = authReturnCookie
        ? authReturnCookie.value
        : appUrl;

    const localState = cookieStore.get('authorizeState');

    if (!localState) {
        return new Response('Incorrect state', { status: 400 });
    }

    const { searchParams } = new URL(request.url);

    const urlState = searchParams.get('state');

    if (localState.value !== urlState) {
        return new Response('Incorrect state', { status: 400 });
    }

    const code = searchParams.get('code');

    let callbackUrl = appUrl.endsWith('/')
        ? appUrl.slice(0, -1)
        : appUrl;

    callbackUrl += callbackUri;

    const tokenResponse = await fetch(
        tokenUrl,
        {
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            method: 'POST',
            body: JSON.stringify({
                grant_type: 'authorization_code',
                redirect_uri: callbackUrl,
                client_id: clientId,
                client_secret: clientSecret,
                code,
            }),
        },
    );

    try {
        const responseJson = await tokenResponse.json();

        if (tokenResponse.status !== 200) {
            const errorJson = responseJson as ErrorJson;

            const error = errorJson.error ?? 'An unknown error occurred';

            const errorDescription = errorJson.error_description ?? '';

            return new Response(
                `${error}\n\n${errorDescription}`,
                { status: tokenResponse.status },
            );
        }

        const tokenJson = responseJson as TokenResponseJson;

        const userInfoRequest = await fetch(
            userInfoUrl,
            {
                redirect: 'manual',
                method: 'GET',
                headers: {
                    Authorization: `Bearer ${tokenJson.access_token}`,
                    RequestType: 'api',
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
            },
        );

        const userInfoJson = await userInfoRequest.json() as UserInfoJson;

        const jwt: NextAuthJwt = {
            accessToken: tokenJson.access_token,
            accessTokenExpires: (new Date().getTime()) + tokenJson.expires_in,
            refreshToken: tokenJson.refresh_token,
            user: {
                id: userInfoJson.sub,
                sub: userInfoJson.sub,
                email: userInfoJson.email,
                name: userInfoJson.name,
            },
        };

        const sessionId = randomBytes(32).toString('hex');

        await tokenRepository.setTokenFromSessionId(
            jwt,
            sessionId,
        );

        cookieStore.set('oauthSessionId', sessionId, {
            httpOnly: true,
            path: '/',
            maxAge: 2628000, // One month for good measure
            secure: true,
        });

        return Response.redirect(authReturn);
    } catch (error) {
        return new Response(
            'An unknown error occurred',
            { status: 500 },
        );
    }
}
