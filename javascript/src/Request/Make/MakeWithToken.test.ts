// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    describe, it, expect, vi, beforeEach, afterEach,
} from 'vitest';
import { MakeWithToken } from './MakeWithToken';
import { AccessDeniedUserNotLoggedInResponse } from './AccessDeniedResponse';
import RequestMethods from '../RequestMethods';
import RequestAuthenticationError from '../RequestAuthenticationError';

vi.mock('./ParseResponse', () => ({
    ParseResponse: async (fn: any) => fn(),
}));

describe('MakeWithToken', () => {
    const baseUrl = 'https://api.example.com';

    const nextAuthProviderId = undefined;

    let fetchMock: ReturnType<typeof vi.fn>;

    let tokenRepository: any;

    let refreshAccessToken: any;

    const validToken = { accessToken: 'abc', refreshToken: 'def' };

    const newToken = { accessToken: 'xyz', refreshToken: 'uvw' };

    const props = {
        uri: '/test',
        method: RequestMethods.POST,
        queryParams: new URLSearchParams(),
        payload: { foo: 'bar' },
        cacheTags: [],
        cacheSeconds: 0,
    };

    beforeEach(() => {
        fetchMock = vi.fn();

        global.fetch = fetchMock;

        refreshAccessToken = vi.fn();
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it(
        'returns AccessDeniedUserNotLoggedInResponse if no token is found',
        async () => {
            tokenRepository = {
                findTokenFromCookies: vi.fn().mockResolvedValue(null),
            };

            const result = await MakeWithToken(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
            );

            expect(result).toBe(AccessDeniedUserNotLoggedInResponse);

            expect(tokenRepository.findTokenFromCookies).toHaveBeenCalledTimes(
                1,
            );

            expect(fetchMock).not.toHaveBeenCalled();
        },
    );

    it(
        'makes a request with a valid token and returns the response',
        async () => {
            tokenRepository = {
                findTokenFromCookies: vi.fn().mockResolvedValue(validToken),
            };

            fetchMock.mockResolvedValue({
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            });

            const result = await MakeWithToken(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
            );

            expect(fetchMock).toHaveBeenCalledTimes(1);

            expect(result.status).toBe(200);

            expect(refreshAccessToken).not.toHaveBeenCalled();
        },
    );

    it(
        'retries with refreshed token if first response is 401',
        async () => {
            tokenRepository = {
                findTokenFromCookies: vi.fn()
                    .mockResolvedValueOnce(validToken)
                    .mockResolvedValueOnce(newToken),
            };

            fetchMock
                .mockResolvedValueOnce({
                    status: 401,
                    ok: false,
                    json: async () => ({ error: 'unauthorized' }),
                })
                .mockResolvedValueOnce({
                    status: 200,
                    ok: true,
                    json: async () => ({ data: 'ok' }),
                });

            const result = await MakeWithToken(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
            );

            expect(fetchMock).toHaveBeenCalledTimes(2);

            expect(refreshAccessToken).toHaveBeenCalledTimes(1);

            expect(result.status).toBe(200);
        },
    );

    it(
        'returns 401 response if retry after refresh is also 401',
        async () => {
            tokenRepository = {
                findTokenFromCookies: vi.fn()
                    .mockResolvedValueOnce(validToken)
                    .mockResolvedValueOnce(newToken),
            };

            fetchMock
                .mockResolvedValueOnce({
                    status: 401,
                    ok: false,
                    json: async () => ({ error: 'unauthorized' }),
                })
                .mockResolvedValueOnce({
                    status: 401,
                    ok: false,
                    json: async () => ({ error: 'unauthorized' }),
                });

            const result = await MakeWithToken(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
            );

            expect(fetchMock).toHaveBeenCalledTimes(2);

            expect(refreshAccessToken).toHaveBeenCalledTimes(2);

            expect(result.status).toBe(401);
        },
    );

    it(
        'returns 401 response if no new token is found after refresh',
        async () => {
            tokenRepository = {
                findTokenFromCookies: vi.fn()
                    .mockResolvedValueOnce(validToken)
                    .mockResolvedValueOnce(null),
            };

            fetchMock.mockResolvedValueOnce({
                status: 401,
                ok: false,
                json: async () => ({ error: 'unauthorized' }),
            });

            const result = await MakeWithToken(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
            );

            expect(fetchMock).toHaveBeenCalledTimes(1);

            expect(refreshAccessToken).toHaveBeenCalledTimes(1);

            expect(result.status).toBe(401);
        },
    );

    it(
        'throws RequestAuthenticationError if token.accessToken is null',
        async () => {
            tokenRepository = {
                findTokenFromCookies: vi.fn().mockResolvedValue({
                    accessToken: null,
                    refreshToken: 'def',
                }),
            };

            await expect(MakeWithToken(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
            )).rejects.toThrow(RequestAuthenticationError);
        },
    );

    it(
        'sets Provider header when nextAuthProviderId is provided',
        async () => {
            tokenRepository = {
                findTokenFromCookies: vi.fn().mockResolvedValue(validToken),
            };

            fetchMock.mockResolvedValue({
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            });

            const customProviderId = 'custom-provider';

            await MakeWithToken(
                props,
                baseUrl,
                customProviderId,
                tokenRepository,
                refreshAccessToken,
            );

            const [, options] = fetchMock.mock.calls[0];
            expect(options.headers.get('Provider')).toBe(customProviderId);
        },
    );
});
