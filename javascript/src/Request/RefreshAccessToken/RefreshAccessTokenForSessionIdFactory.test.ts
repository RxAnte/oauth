// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    beforeEach, describe, expect, it, vi,
} from 'vitest';
import Redis from 'ioredis';
import { RefreshAccessTokenForSessionIdFactory } from './RefreshAccessTokenForSessionIdFactory';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { TokenData } from '../../TokenData';
import { RefreshLock } from './Lock/RefreshLock';
import { GetWellKnown, WellKnown } from '../../WellKnown';

vi.mock('../../WellKnown', () => ({
    GetWellKnown: vi.fn(),
}));

describe('RefreshAccessTokenForSessionIdFactory', () => {
    const sessionId = 'session-123';

    // @ts-expect-error TS2739
    const mockTokenRepository: TokenRepository = {
        findTokenBySessionId: vi.fn(),
        setTokenFromSessionId: vi.fn(),
        setTokenBasedOnCookies: vi.fn(),
    };

    const mockRefreshLock: RefreshLock = {
        acquire: vi.fn(() => Promise.resolve()),
        release: vi.fn(() => Promise.resolve()),
    };

    // @ts-expect-error TS2739
    const wellKnownReturn: WellKnown = {
        tokenEndpoint: 'https://auth.com/token',
    };

    const mockRedisInstance = {} as Redis;

    beforeEach(() => {
        vi.resetAllMocks();

        global.fetch = vi.fn();

        vi.mocked(GetWellKnown).mockReturnValue(
            Promise.resolve(wellKnownReturn),
        );
    });

    it(
        'returns early without acquiring a lock if no token is found for the session',
        async () => {
            vi.mocked(mockTokenRepository.findTokenBySessionId).mockResolvedValue(
                null,
            );

            const refreshAccessToken = RefreshAccessTokenForSessionIdFactory({
                tokenRepository: mockTokenRepository,
                refreshLock: mockRefreshLock,
                wellKnownUrl: 'https://auth.com/well-known',
                clientId: 'mock-client-id',
                clientSecret: 'mock-client-secret',
            });

            await refreshAccessToken(sessionId);

            expect(mockTokenRepository.findTokenBySessionId).toHaveBeenCalledWith(
                sessionId,
            );

            expect(mockRefreshLock.acquire).not.toHaveBeenCalled();

            expect(global.fetch).not.toHaveBeenCalled();
        },
    );

    it(
        'does not refresh the token if it was already refreshed while awaiting a lock',
        async () => {
            const findTokenBySessionIdCount = {
                count: 0,
            };

            vi.mocked(
                mockTokenRepository.findTokenBySessionId,
            ).mockImplementation(async () => {
                findTokenBySessionIdCount.count += 1;

                const { count } = findTokenBySessionIdCount;

                return {
                    accessToken: count === 1
                        ? 'access-token-1'
                        : 'access-token-2',
                } as TokenData;
            });

            const refreshAccessToken = RefreshAccessTokenForSessionIdFactory({
                tokenRepository: mockTokenRepository,
                refreshLock: mockRefreshLock,
                wellKnownUrl: 'https://auth.com/well-known',
                clientId: 'mock-client-id',
                clientSecret: 'mock-client-secret',
            });

            await refreshAccessToken(sessionId);

            expect(global.fetch).not.toHaveBeenCalled();

            expect(mockRefreshLock.acquire).toHaveBeenCalledWith(
                'access-token-1',
            );

            expect(mockRefreshLock.release).toHaveBeenCalledWith(
                'access-token-1',
            );
        },
    );

    it(
        'attempts to refresh the token and releases the lock if there is an error',
        async () => {
            vi.mocked(mockTokenRepository.findTokenBySessionId).mockReturnValue(
                Promise.resolve({
                    accessToken: 'access-token',
                    refreshToken: 'refresh-token',
                } as TokenData),
            );

            (global.fetch as any).mockRejectedValue(
                new Error('Test error'),
            );

            const refreshAccessToken = RefreshAccessTokenForSessionIdFactory({
                tokenRepository: mockTokenRepository,
                refreshLock: mockRefreshLock,
                wellKnownUrl: 'https://auth.com/well-known',
                clientId: 'mock-client-id',
                clientSecret: 'mock-client-secret',
            });

            await refreshAccessToken(sessionId);

            expect(GetWellKnown).toHaveBeenCalledWith(
                'https://auth.com/well-known',
                undefined,
                'rxante_oauth_well_known',
                86400,
            );

            expect(global.fetch).toHaveBeenCalledWith(
                'https://auth.com/token',
                {
                    headers: { 'Content-Type': 'application/json' },
                    method: 'POST',
                    body: JSON.stringify({
                        grant_type: 'refresh_token',
                        refresh_token: 'refresh-token',
                        client_id: 'mock-client-id',
                        client_secret: 'mock-client-secret',
                    }),
                },
            );

            expect(mockRefreshLock.acquire).toHaveBeenCalledWith(
                'access-token',
            );

            expect(mockRefreshLock.release).toHaveBeenCalledWith(
                'access-token',
            );
        },
    );

    it(
        'attempts to refresh the token and releases the lock if token response is not okay',
        async () => {
            vi.mocked(mockTokenRepository.findTokenBySessionId).mockReturnValue(
                Promise.resolve({
                    accessToken: 'access-token',
                    refreshToken: 'refresh-token',
                } as TokenData),
            );

            (global.fetch as any).mockResolvedValue({ ok: false });

            const refreshAccessToken = RefreshAccessTokenForSessionIdFactory({
                tokenRepository: mockTokenRepository,
                refreshLock: mockRefreshLock,
                wellKnownUrl: 'https://auth.com/well-known/mock',
                clientId: 'client-id',
                clientSecret: 'client-secret',
                redis: mockRedisInstance,
                wellKnownCacheKey: 'test-key',
                wellKnownCacheExpiresInSeconds: 123,
            });

            await refreshAccessToken(sessionId);

            expect(GetWellKnown).toHaveBeenCalledWith(
                'https://auth.com/well-known/mock',
                mockRedisInstance,
                'test-key',
                123,
            );

            expect(global.fetch).toHaveBeenCalledWith(
                'https://auth.com/token',
                {
                    headers: { 'Content-Type': 'application/json' },
                    method: 'POST',
                    body: JSON.stringify({
                        grant_type: 'refresh_token',
                        refresh_token: 'refresh-token',
                        client_id: 'client-id',
                        client_secret: 'client-secret',
                    }),
                },
            );

            expect(mockRefreshLock.acquire).toHaveBeenCalledWith(
                'access-token',
            );

            expect(mockRefreshLock.release).toHaveBeenCalledWith(
                'access-token',
            );
        },
    );

    it(
        'refreshes the token in Redis by session id, not via cookies, and releases the lock',
        async () => {
            vi.useFakeTimers();
            vi.setSystemTime(new Date('2024-01-01T00:00:00Z'));

            vi.mocked(mockTokenRepository.findTokenBySessionId).mockReturnValue(
                Promise.resolve({
                    accessToken: 'access-token-mock',
                    refreshToken: 'refresh-token-mock',
                } as TokenData),
            );

            (global.fetch as any).mockResolvedValue({
                ok: true,
                json: () => Promise.resolve({
                    access_token: 'new-access-token',
                    expires_in: 987,
                    refresh_token: 'new-refresh-token',
                }),
            });

            const refreshAccessToken = RefreshAccessTokenForSessionIdFactory({
                tokenRepository: mockTokenRepository,
                refreshLock: mockRefreshLock,
                wellKnownUrl: 'https://auth.com/well-known',
                clientId: 'test-client-id',
                clientSecret: 'test-client-secret',
            });

            await refreshAccessToken(sessionId);

            expect(GetWellKnown).toHaveBeenCalledWith(
                'https://auth.com/well-known',
                undefined,
                'rxante_oauth_well_known',
                86400,
            );

            expect(global.fetch).toHaveBeenCalledWith(
                'https://auth.com/token',
                {
                    headers: { 'Content-Type': 'application/json' },
                    method: 'POST',
                    body: JSON.stringify({
                        grant_type: 'refresh_token',
                        refresh_token: 'refresh-token-mock',
                        client_id: 'test-client-id',
                        client_secret: 'test-client-secret',
                    }),
                },
            );

            expect(
                mockTokenRepository.setTokenFromSessionId,
            ).toHaveBeenCalledWith(
                {
                    accessToken: 'new-access-token',
                    refreshToken: 'new-refresh-token',
                    accessTokenExpires: (new Date().getTime()) + 987,
                },
                sessionId,
            );

            expect(
                mockTokenRepository.setTokenBasedOnCookies,
            ).not.toHaveBeenCalled();

            expect(mockRefreshLock.acquire).toHaveBeenCalledWith(
                'access-token-mock',
            );

            expect(mockRefreshLock.release).toHaveBeenCalledWith(
                'access-token-mock',
            );

            vi.useRealTimers();
        },
    );
});
