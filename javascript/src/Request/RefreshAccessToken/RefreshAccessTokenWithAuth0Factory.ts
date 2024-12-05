import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshLock } from './Lock/RefreshLock';
import { RefreshAccessToken } from './RefreshAccessToken';
import { NextAuthJwt } from '../../NextAuth/NextAuthJwt';

async function requestRefreshedToken (
    token: NextAuthJwt,
    wellKnownUrl: string,
    clientId: string,
    clientSecret: string,
) {
    const wellKnownConfig = {
        headers: new Headers({
            'Content-Type': 'application/json',
        }),
    };

    const wellKnownResp = await fetch(
        wellKnownUrl,
        wellKnownConfig,
    );

    const wellKnownJson = await wellKnownResp.json();

    const tokenUrl = wellKnownJson.token_endpoint;

    const { refreshToken } = token;

    const refreshConfig = {
        headers: { 'Content-Type': 'application/json' },
        method: 'POST',
        body: JSON.stringify({
            grant_type: 'refresh_token',
            refresh_token: refreshToken,
            client_id: clientId,
            client_secret: clientSecret,
        }),
    };

    return fetch(
        tokenUrl,
        refreshConfig,
    );
}

async function getRefreshedAccessToken (
    token: NextAuthJwt,
    wellKnownUrl: string,
    clientId: string,
    clientSecret: string,
) {
    try {
        const refreshResponse = await requestRefreshedToken(
            token,
            wellKnownUrl,
            clientId,
            clientSecret,
        );

        if (!refreshResponse.ok) {
            return null;
        }

        const refreshedJson = await refreshResponse.json();

        return {
            ...token,
            accessToken: refreshedJson.access_token,
            accessTokenExpires: (new Date().getTime()) + refreshedJson.expires_in,
            refreshToken: refreshedJson.refresh_token,
        };
    } catch (error) {
        return null;
    }
}

export function RefreshAccessTokenWithAuth0Factory (
    {
        tokenRepository,
        refreshLock,
        wellKnownUrl,
        clientId,
        clientSecret,
    }: {
        tokenRepository: TokenRepository;
        refreshLock: RefreshLock;
        wellKnownUrl: string;
        clientId: string;
        clientSecret: string;
    },
): RefreshAccessToken {
    return async () => {
        const token = await tokenRepository.getTokenFromCookies();

        // To ensure that only one request is refreshing the token we await a lock
        await refreshLock.acquire(token.accessToken);

        /**
         * Now we check the token in the store again to make sure the token wasn't
         * already refreshed by another request
         */
        const tokenCheck = await tokenRepository.getTokenFromCookies();

        // If the token was already refreshed while we awaited a lock
        if (tokenCheck.accessToken !== token.accessToken) {
            await refreshLock.release(token.accessToken);

            return;
        }

        const newToken = await getRefreshedAccessToken(
            token,
            wellKnownUrl,
            clientId,
            clientSecret,
        );

        // If there is no token, the refresh was unsuccessful, and so we won't save
        if (!newToken) {
            await refreshLock.release(token.accessToken);

            return;
        }

        // WE HAVE A NEW TOKEN! YAY! Now set it to the token store
        await tokenRepository.setTokenBasedOnCookies(newToken);

        await refreshLock.release(token.accessToken);
    };
}
