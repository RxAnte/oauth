// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    describe, it, expect, vi, beforeEach, afterEach,
} from 'vitest';
import { MakeWithTokenForSessionId } from './MakeWithTokenForSessionId';
import { AccessDeniedUserNotLoggedInResponse } from './AccessDeniedResponse';
import RequestMethods from '../RequestMethods';
import RequestAuthenticationError from '../RequestAuthenticationError';

vi.mock('./ParseResponse', () => ({
    ParseResponse: async (fn: any) => fn(),
}));

describe('MakeWithTokenForSessionId', () => {
    const baseUrl = 'https://api.example.com';

    const nextAuthProviderId = undefined;

    const sessionId = 'session-123';

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
        headers: {},
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
                findTokenBySessionId: vi.fn().mockResolvedValue(null),
            };

            const result = await MakeWithTokenForSessionId(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
            );

            expect(result).toBe(AccessDeniedUserNotLoggedInResponse);

            expect(tokenRepository.findTokenBySessionId).toHaveBeenCalledWith(
                sessionId,
            );

            expect(fetchMock).not.toHaveBeenCalled();
        },
    );

    it(
        'makes a request with a valid token and returns the response',
        async () => {
            tokenRepository = {
                findTokenBySessionId: vi.fn().mockResolvedValue(validToken),
            };

            fetchMock.mockResolvedValue({
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            });

            const result = await MakeWithTokenForSessionId(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
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
                findTokenBySessionId: vi.fn()
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

            const result = await MakeWithTokenForSessionId(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
            );

            expect(fetchMock).toHaveBeenCalledTimes(2);

            expect(refreshAccessToken).toHaveBeenCalledWith(sessionId);

            expect(result.status).toBe(200);
        },
    );

    it(
        'returns 401 response if retry after refresh is also 401',
        async () => {
            tokenRepository = {
                findTokenBySessionId: vi.fn()
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

            const result = await MakeWithTokenForSessionId(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
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
                findTokenBySessionId: vi.fn()
                    .mockResolvedValueOnce(validToken)
                    .mockResolvedValueOnce(null),
            };

            fetchMock.mockResolvedValueOnce({
                status: 401,
                ok: false,
                json: async () => ({ error: 'unauthorized' }),
            });

            const result = await MakeWithTokenForSessionId(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
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
                findTokenBySessionId: vi.fn().mockResolvedValue({
                    accessToken: null,
                    refreshToken: 'def',
                }),
            };

            await expect(MakeWithTokenForSessionId(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
            )).rejects.toThrow(RequestAuthenticationError);
        },
    );

    it(
        'sets Provider header when nextAuthProviderId is provided',
        async () => {
            tokenRepository = {
                findTokenBySessionId: vi.fn().mockResolvedValue(validToken),
            };

            fetchMock.mockResolvedValue({
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            });

            const customProviderId = 'custom-provider';

            await MakeWithTokenForSessionId(
                props,
                baseUrl,
                customProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
            );

            const [, options] = fetchMock.mock.calls[0];
            expect(options.headers.get('Provider')).toBe(customProviderId);
        },
    );

    it(
        'merges caller supplied custom headers into the request',
        async () => {
            tokenRepository = {
                findTokenBySessionId: vi.fn().mockResolvedValue(validToken),
            };

            fetchMock.mockResolvedValue({
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            });

            await MakeWithTokenForSessionId(
                { ...props, headers: { PRPClient: 'some-client' } },
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                sessionId,
            );

            const [, options] = fetchMock.mock.calls[0];

            expect(options.headers.get('PRPClient')).toBe('some-client');
        },
    );

    it(
        'does not allow custom headers to override managed headers',
        async () => {
            tokenRepository = {
                findTokenBySessionId: vi.fn().mockResolvedValue(validToken),
            };

            fetchMock.mockResolvedValue({
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            });

            await MakeWithTokenForSessionId(
                {
                    ...props,
                    headers: {
                        Authorization: 'Bearer tampered',
                        RequestType: 'not-api',
                        'Content-Type': 'text/plain',
                        Provider: 'not-auth0',
                    },
                },
                baseUrl,
                'auth0',
                tokenRepository,
                refreshAccessToken,
                sessionId,
            );

            const [, options] = fetchMock.mock.calls[0];

            expect(options.headers.get('Authorization')).toBe(
                `Bearer ${validToken.accessToken}`,
            );

            expect(options.headers.get('RequestType')).toBe('api');

            expect(options.headers.get('Content-Type')).toBe(
                'application/json',
            );

            expect(options.headers.get('Provider')).toBe('auth0');
        },
    );
});
