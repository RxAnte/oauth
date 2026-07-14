// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    describe, it, expect, vi, beforeEach, afterEach,
} from 'vitest';
import { MakeWithSignInRedirectForSessionId } from './MakeWithSignInRedirectForSessionId';
import { MakeWithTokenForSessionId } from './MakeWithTokenForSessionId';
import { RequestResponse } from '../RequestResponse';
import { OAUTH_SESSION_ID_COOKIE } from '../../TokenRepository/OAuthSessionIdCookie';
import RequestMethods from '../RequestMethods';

vi.mock('./MakeWithTokenForSessionId');

describe('MakeWithSignInRedirectForSessionId', () => {
    const baseUrl = 'https://api.example.com';

    const signInUri = '/api/auth/sign-in';

    const nextAuthProviderId = undefined;

    let tokenRepository: any;

    let refreshAccessToken: any;

    let MakeWithTokenForSessionIdMock: ReturnType<
        typeof vi.mocked<typeof MakeWithTokenForSessionId>
    >;

    const props = {
        uri: '/test',
        method: RequestMethods.GET,
        queryParams: new URLSearchParams(),
        payload: {},
        cacheTags: [],
        cacheSeconds: 0,
        headers: {},
    };

    beforeEach(() => {
        MakeWithTokenForSessionIdMock = vi.mocked(MakeWithTokenForSessionId);

        tokenRepository = {};

        refreshAccessToken = vi.fn();

        vi.clearAllMocks();
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it(
        'redirects to sign-in without making a request if no session id cookie is present',
        async () => {
            const req = { cookies: {} };

            const result = await MakeWithSignInRedirectForSessionId(
                props,
                req,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                signInUri,
            );

            expect(result).toEqual({
                redirect: {
                    destination: signInUri,
                    permanent: false,
                },
            });

            expect(MakeWithTokenForSessionIdMock).not.toHaveBeenCalled();
        },
    );

    it(
        'returns the response if the session id is present and status is not 401',
        async () => {
            const req = { cookies: { [OAUTH_SESSION_ID_COOKIE]: 'session-123' } };

            const goodResponse = {
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            } as unknown as RequestResponse;

            MakeWithTokenForSessionIdMock.mockResolvedValue(goodResponse);

            const result = await MakeWithSignInRedirectForSessionId(
                props,
                req,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                signInUri,
            );

            expect(result).toBe(goodResponse);

            expect(MakeWithTokenForSessionIdMock).toHaveBeenCalledWith(
                props,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                'session-123',
            );
        },
    );

    it(
        'redirects to sign-in if the response status is 401',
        async () => {
            const req = { cookies: { [OAUTH_SESSION_ID_COOKIE]: 'session-123' } };

            const unauthorizedResponse = {
                status: 401,
                ok: false,
                json: async () => ({ error: 'unauthorized' }),
            } as unknown as RequestResponse;

            MakeWithTokenForSessionIdMock.mockResolvedValue(unauthorizedResponse);

            const result = await MakeWithSignInRedirectForSessionId(
                props,
                req,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                signInUri,
            );

            expect(result).toEqual({
                redirect: {
                    destination: signInUri,
                    permanent: false,
                },
            });
        },
    );
});
