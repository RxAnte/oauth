// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    describe, it, expect, vi, beforeEach, afterEach,
} from 'vitest';
import { redirect } from 'next/navigation';
import { headers } from 'next/headers';
import { MakeWithSignInRedirect } from './MakeWithSignInRedirect';
import { RequestResponse } from '../RequestResponse';
import RequestMethods from '../RequestMethods';
import { MakeWithToken } from './MakeWithToken';

vi.mock('./MakeWithToken');
vi.mock('next/navigation');
vi.mock('next/headers');

describe('MakeWithSignInRedirect', () => {
    const appUrl = 'https://app.example.com';

    const baseUrl = 'https://api.example.com';

    const signInUri = '/api/auth/sign-in';

    const nextAuthProviderId = undefined;

    let tokenRepository: any;

    let refreshAccessToken: any;

    let MakeWithTokenMock: ReturnType<typeof vi.mocked<typeof MakeWithToken>>;

    let redirectMock: ReturnType<typeof vi.mocked<typeof redirect>>;

    let headersMock: ReturnType<typeof vi.mocked<typeof headers>>;

    const props = {
        uri: '/test',
        method: RequestMethods.GET,
        queryParams: new URLSearchParams(),
        payload: {},
        cacheTags: [],
        cacheSeconds: 0,
    };

    beforeEach(() => {
        MakeWithTokenMock = vi.mocked(MakeWithToken);

        redirectMock = vi.mocked(redirect);

        headersMock = vi.mocked(headers);

        tokenRepository = {};

        refreshAccessToken = vi.fn();

        vi.clearAllMocks();
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it(
        'returns response if status is not 401',
        async () => {
            const goodResponse = {
                status: 200,
                ok: true,
                json: async () => ({ data: 'ok' }),
            } as unknown as RequestResponse;

            MakeWithTokenMock.mockResolvedValue(goodResponse);

            const result = await MakeWithSignInRedirect(
                props,
                appUrl,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                signInUri,
            );

            expect(result).toBe(goodResponse);

            expect(redirectMock).not.toHaveBeenCalled();
        },
    );

    it(
        'calls redirect with correct URL if status is 401 and headers present',
        async () => {
            const unauthorizedResponse = {
                status: 401,
                ok: false,
                json: async () => ({ error: 'unauthorized' }),
            } as unknown as RequestResponse;

            MakeWithTokenMock.mockResolvedValue(unauthorizedResponse);

            headersMock.mockReturnValue({
                get: (key: string) => {
                    if (key === 'middleware-pathname') return '/foo/bar';

                    if (key === 'middleware-search-params') return 'baz=qux';

                    return undefined;
                },
            } as unknown as ReturnType<typeof headers>);

            await MakeWithSignInRedirect(
                props,
                appUrl,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                signInUri,
            );

            const expectedAuthReturn = encodeURIComponent(
                `${appUrl}/foo/bar?baz=qux`,
            );

            const expectedQuery = `authReturn=${expectedAuthReturn}`;

            expect(redirectMock).toHaveBeenCalledWith(
                `${appUrl}${signInUri}?${expectedQuery}`,
            );
        },
    );

    it(
        'calls redirect with root path if headers missing',
        async () => {
            const unauthorizedResponse = {
                status: 401,
                ok: false,
                json: async () => ({ error: 'unauthorized' }),
            } as unknown as RequestResponse;

            MakeWithTokenMock.mockResolvedValue(unauthorizedResponse);

            headersMock.mockReturnValue({
                get: () => undefined,
            } as unknown as ReturnType<typeof headers>);

            await MakeWithSignInRedirect(
                props,
                appUrl,
                baseUrl,
                nextAuthProviderId,
                tokenRepository,
                refreshAccessToken,
                signInUri,
            );

            const expectedAuthReturn = encodeURIComponent(
                `${appUrl}/`,
            );

            const expectedQuery = `authReturn=${expectedAuthReturn}`;

            expect(redirectMock).toHaveBeenCalledWith(
                `${appUrl}${signInUri}?${expectedQuery}`,
            );
        },
    );
});
