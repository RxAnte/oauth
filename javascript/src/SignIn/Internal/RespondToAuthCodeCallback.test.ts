import {
    vi, describe, it, expect, beforeEach,
} from 'vitest';
import { cookies } from 'next/headers';
import RespondToAuthCodeCallback from './RespondToAuthCodeCallback';
import { TokenRepository } from '../../TokenRepository/TokenRepository';

vi.mock('next/headers', () => ({
    cookies: vi.fn(),
}));

const values: {
    authReturnCookie: null | { value: string };
    authorizeStateCookie: null | { value: string };
} = {
    authReturnCookie: null,
    authorizeStateCookie: null,
};

describe('RespondToAuthCodeCallback', () => {
    const mockTokenRepository = {
        setTokenFromSessionId: vi.fn(),
    } as unknown as TokenRepository;

    const mockCookies = {
        get: vi.fn(),
        set: vi.fn(),
    };

    const mockFetch = vi.fn();

    beforeEach(() => {
        vi.resetAllMocks();

        vi.mocked(mockFetch).mockImplementation((url) => {
            if (url.includes('bad-token-response')) {
                return Promise.resolve({
                    status: 503,
                    json: () => Promise.resolve({
                        error: 'A token error occurred',
                        error_description: "We don't know more than that",
                    }),
                });
            }
            if (url.includes('bad-token-json')) {
                return Promise.resolve({
                    status: 200,
                    json: () => Promise.reject(new Error('Bad json')),
                });
            }

            if (url.includes('token')) {
                return Promise.resolve({
                    status: 200,
                    json: () => Promise.resolve({
                        token_type: 'Bearer',
                        expires_in: 3600,
                        access_token: 'mock-access-token',
                        refresh_token: 'mock-refresh-token',
                    }),
                });
            }

            if (url.includes('userinfo')) {
                return Promise.resolve({
                    status: 200,
                    json: () => Promise.resolve({
                        sub: 'mock-sub',
                        email: 'mock@example.com',
                        name: 'Mock User',
                    }),
                });
            }

            return Promise.reject(new Error('Unknown URL'));
        });

        vi.mocked(mockCookies.get).mockImplementation((key) => {
            if (key === 'authorizeState') {
                return values.authorizeStateCookie;
            }
            if (key === 'authReturn') {
                return values.authReturnCookie;
            }

            return undefined;
        });

        vi.mocked(mockCookies.set).mockImplementation(() => {});

        // @ts-expect-error TS2345
        vi.mocked(cookies).mockReturnValue(mockCookies);

        global.fetch = mockFetch;
    });

    it(
        'should handle successful token exchange and user info retrieval',
        async () => {
            values.authReturnCookie = {
                value: 'http://localhost/authReturnValue',
            };

            values.authorizeStateCookie = { value: 'mock-state' };

            const response = await RespondToAuthCodeCallback(
                mockTokenRepository,
                new Request(
                    'http://localhost/callback?state=mock-state&code=mock-code',
                ),
                'http://localhost',
                'http://localhost/token',
                'http://localhost/userinfo',
                'mock-client-id',
                'mock-client-secret',
            );

            expect(mockFetch).toHaveBeenCalledWith(
                'http://localhost/token',
                expect.objectContaining({
                    headers: expect.objectContaining({
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    }),
                    method: 'POST',
                    body: JSON.stringify({
                        grant_type: 'authorization_code',
                        redirect_uri: 'http://localhost/api/auth/callback',
                        client_id: 'mock-client-id',
                        client_secret: 'mock-client-secret',
                        code: 'mock-code',
                    }),
                }),
            );

            expect(mockFetch).toHaveBeenCalledWith(
                'http://localhost/userinfo',
                expect.objectContaining({
                    redirect: 'manual',
                    method: 'GET',
                    headers: expect.objectContaining({
                        Authorization: 'Bearer mock-access-token',
                        RequestType: 'api',
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    }),
                }),
            );

            expect(mockTokenRepository.setTokenFromSessionId).toHaveBeenCalledWith(
                expect.objectContaining({
                    accessToken: 'mock-access-token',
                    refreshToken: 'mock-refresh-token',
                    user: expect.objectContaining({
                        id: 'mock-sub',
                        email: 'mock@example.com',
                        name: 'Mock User',
                    }),
                }),
                expect.any(String),
            );

            expect(mockCookies.set).toHaveBeenCalledWith(
                'oauthSessionId',
                expect.any(String),
                expect.any(Object),
            );

            expect(response.headers.get('location')).toBe(
                'http://localhost/authReturnValue',
            );

            expect(response.status).toBe(302);
        },
    );

    it(
        'should handle successful token exchange and user info retrieval when an incorrect return URL host has been specified',
        async () => {
            values.authReturnCookie = {
                value: 'http://malicioushost/authReturnValue',
            };

            values.authorizeStateCookie = { value: 'mock-state' };

            const response = await RespondToAuthCodeCallback(
                mockTokenRepository,
                new Request(
                    'http://localhost/callback?state=mock-state&code=mock-code',
                ),
                'http://localhost/',
                'http://localhost/token',
                'http://localhost/userinfo',
                'mock-client-id',
                'mock-client-secret',
            );

            expect(mockFetch).toHaveBeenCalledWith(
                'http://localhost/token',
                expect.objectContaining({
                    headers: expect.objectContaining({
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    }),
                    method: 'POST',
                    body: JSON.stringify({
                        grant_type: 'authorization_code',
                        redirect_uri: 'http://localhost/api/auth/callback',
                        client_id: 'mock-client-id',
                        client_secret: 'mock-client-secret',
                        code: 'mock-code',
                    }),
                }),
            );

            expect(mockFetch).toHaveBeenCalledWith(
                'http://localhost/userinfo',
                expect.objectContaining({
                    redirect: 'manual',
                    method: 'GET',
                    headers: expect.objectContaining({
                        Authorization: 'Bearer mock-access-token',
                        RequestType: 'api',
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    }),
                }),
            );

            expect(mockTokenRepository.setTokenFromSessionId).toHaveBeenCalledWith(
                expect.objectContaining({
                    accessToken: 'mock-access-token',
                    refreshToken: 'mock-refresh-token',
                    user: expect.objectContaining({
                        id: 'mock-sub',
                        email: 'mock@example.com',
                        name: 'Mock User',
                    }),
                }),
                expect.any(String),
            );

            expect(mockCookies.set).toHaveBeenCalledWith(
                'oauthSessionId',
                expect.any(String),
                expect.any(Object),
            );

            expect(response.headers.get('location')).toBe(
                'http://localhost/',
            );

            expect(response.status).toBe(302);
        },
    );

    it(
        'should handle successful token exchange and user info retrieval when no authReturn cookie is present',
        async () => {
            values.authReturnCookie = null;

            values.authorizeStateCookie = { value: 'mock-state' };

            const response = await RespondToAuthCodeCallback(
                mockTokenRepository,
                new Request(
                    'http://localhost/callback?state=mock-state&code=mock-code',
                ),
                'http://localhost',
                'http://localhost/token',
                'http://localhost/userinfo',
                'mock-client-id',
                'mock-client-secret',
            );

            expect(mockFetch).toHaveBeenCalledWith(
                'http://localhost/token',
                expect.objectContaining({
                    headers: expect.objectContaining({
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    }),
                    method: 'POST',
                    body: JSON.stringify({
                        grant_type: 'authorization_code',
                        redirect_uri: 'http://localhost/api/auth/callback',
                        client_id: 'mock-client-id',
                        client_secret: 'mock-client-secret',
                        code: 'mock-code',
                    }),
                }),
            );

            expect(mockFetch).toHaveBeenCalledWith(
                'http://localhost/userinfo',
                expect.objectContaining({
                    redirect: 'manual',
                    method: 'GET',
                    headers: expect.objectContaining({
                        Authorization: 'Bearer mock-access-token',
                        RequestType: 'api',
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    }),
                }),
            );

            expect(mockTokenRepository.setTokenFromSessionId).toHaveBeenCalledWith(
                expect.objectContaining({
                    accessToken: 'mock-access-token',
                    refreshToken: 'mock-refresh-token',
                    user: expect.objectContaining({
                        id: 'mock-sub',
                        email: 'mock@example.com',
                        name: 'Mock User',
                    }),
                }),
                expect.any(String),
            );

            expect(mockCookies.set).toHaveBeenCalledWith(
                'oauthSessionId',
                expect.any(String),
                expect.any(Object),
            );

            expect(response.headers.get('location')).toBe(
                'http://localhost/',
            );

            expect(response.status).toBe(302);
        },
    );

    it(
        'should return 400 if no local state exists',
        async () => {
            values.authReturnCookie = null;

            values.authorizeStateCookie = null;

            const response = await RespondToAuthCodeCallback(
                mockTokenRepository,
                new Request(
                    'http://localhost/callback?state=mock-state&code=mock-code',
                ),
                'http://localhost',
                'http://localhost/token',
                'http://localhost/userinfo',
                'mock-client-id',
                'mock-client-secret',
            );

            expect(mockFetch).not.toHaveBeenCalled();

            expect(mockCookies.set).not.toHaveBeenCalled();

            expect(response.status).toBe(400);

            expect(await response.text()).toBe('Incorrect state');
        },
    );

    it(
        'should return 400 if no local state does not match url state',
        async () => {
            values.authReturnCookie = null;

            values.authorizeStateCookie = { value: 'not-mock-state' };

            const response = await RespondToAuthCodeCallback(
                mockTokenRepository,
                new Request(
                    'http://localhost/callback?state=mock-state&code=mock-code',
                ),
                'http://localhost',
                'http://localhost/token',
                'http://localhost/userinfo',
                'mock-client-id',
                'mock-client-secret',
            );

            expect(mockFetch).not.toHaveBeenCalled();

            expect(mockCookies.set).not.toHaveBeenCalled();

            expect(response.status).toBe(400);

            expect(await response.text()).toBe('Incorrect state');
        },
    );

    it(
        'should return error if token response fails',
        async () => {
            values.authReturnCookie = null;

            values.authorizeStateCookie = { value: 'mock-state' };

            const response = await RespondToAuthCodeCallback(
                mockTokenRepository,
                new Request(
                    'http://localhost/callback?state=mock-state&code=mock-code',
                ),
                'http://localhost',
                'http://localhost/bad-token-response',
                'http://localhost/userinfo',
                'mock-client-id',
                'mock-client-secret',
            );

            expect(mockCookies.set).not.toHaveBeenCalled();

            expect(response.status).toBe(503);

            expect(await response.text()).toBe(
                'A token error occurred\n\nWe don\'t know more than that',
            );
        },
    );

    it(
        'should return 500 on unknown error',
        async () => {
            values.authReturnCookie = null;

            values.authorizeStateCookie = { value: 'mock-state' };

            const response = await RespondToAuthCodeCallback(
                mockTokenRepository,
                new Request(
                    'http://localhost/callback?state=mock-state&code=mock-code',
                ),
                'http://localhost',
                'http://localhost/bad-token-json',
                'http://localhost/userinfo',
                'mock-client-id',
                'mock-client-secret',
            );

            expect(mockCookies.set).not.toHaveBeenCalled();

            expect(response.status).toBe(500);

            expect(await response.text()).toBe('An unknown error occurred');
        },
    );
});
