// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshLock } from './Lock/RefreshLock';
import { RefreshAccessToken } from './RefreshAccessToken';
import { TokenData } from '../../TokenData';
import { GetWellKnown } from '../../WellKnown';

async function requestRefreshedToken (
    token: TokenData,
    wellKnownUrl: string,
    clientId: string,
    clientSecret: string,
    redis?: Redis,
    wellKnownCacheKey: string = 'rxante_oauth_well_known',
    wellKnownCacheExpiresInSeconds: number = 86400, // cache for 1 day by default
) {
    const wellKnown = await GetWellKnown(
        wellKnownUrl,
        redis,
        wellKnownCacheKey,
        wellKnownCacheExpiresInSeconds,
    );

    const { refreshToken } = token;

    return fetch(
        wellKnown.tokenEndpoint,
        {
            headers: { 'Content-Type': 'application/json' },
            method: 'POST',
            body: JSON.stringify({
                grant_type: 'refresh_token',
                refresh_token: refreshToken,
                client_id: clientId,
                client_secret: clientSecret,
            }),
        },
    );
}

async function getRefreshedAccessToken (
    token: TokenData,
    wellKnownUrl: string,
    clientId: string,
    clientSecret: string,
    redis?: Redis,
    wellKnownCacheKey: string = 'rxante_oauth_well_known',
    wellKnownCacheExpiresInSeconds: number = 86400, // cache for 1 day by default
) {
    try {
        const refreshResponse = await requestRefreshedToken(
            token,
            wellKnownUrl,
            clientId,
            clientSecret,
            redis,
            wellKnownCacheKey,
            wellKnownCacheExpiresInSeconds,
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

export function RefreshAccessTokenFactory (
    {
        tokenRepository,
        refreshLock,
        wellKnownUrl,
        clientId,
        clientSecret,
        redis,
        wellKnownCacheKey = 'rxante_oauth_well_known',
        wellKnownCacheExpiresInSeconds = 86400, // cache for 1 day by default
    }: {
        tokenRepository: TokenRepository;
        refreshLock: RefreshLock;
        wellKnownUrl: string;
        clientId: string;
        clientSecret: string;
        redis?: Redis;
        wellKnownCacheKey?: string;
        wellKnownCacheExpiresInSeconds?: number;
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
            redis,
            wellKnownCacheKey,
            wellKnownCacheExpiresInSeconds,
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
