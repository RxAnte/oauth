import {
    describe,
    it,
    expect,
    vi,
} from 'vitest';

import { RequestFactory } from './RequestFactory';
import * as MakeWithoutTokenModule from './Make/MakeWithoutToken';
import * as MakeWithTokenModule from './Make/MakeWithToken';
import * as MakeWithSignInRedirectModule from './Make/MakeWithSignInRedirect';

const mockTokenRepository = {};
const mockRefreshAccessToken = {};

const baseProps = {
    appUrl: 'https://app.example.com',
    requestBaseUrl: 'https://api.example.com',
    tokenRepository: mockTokenRepository,
    nextAuthProviderId: 'next-auth',
    refreshAccessToken: mockRefreshAccessToken,
    signInUri: '/api/auth/sign-in',
};

describe('RequestFactory', () => {
    it(
        'returns an object with the correct methods',
        () => {
            // @ts-expect-error TS2345
            const factory = RequestFactory(baseProps);

            expect(typeof factory.makeWithoutToken).toBe('function');

            expect(typeof factory.makeWithToken).toBe('function');

            expect(typeof factory.makeWithSignInRedirect).toBe(
                'function',
            );
        },
    );

    it(
        'delegates to MakeWithoutToken with correct arguments',
        async () => {
            const spy = vi.spyOn(
                MakeWithoutTokenModule,
                'MakeWithoutToken',
            ).mockResolvedValue(
                // @ts-expect-error TS2345
                {
                    ok: true,
                },
            );

            // @ts-expect-error TS2345
            const factory = RequestFactory(baseProps);

            const props = {
                uri: '/foo',
            };

            const result = await factory.makeWithoutToken(props);

            expect(spy).toHaveBeenCalledWith(
                {
                    uri: '/foo',
                    method: expect.anything(),
                    queryParams: expect.anything(),
                    payload: expect.anything(),
                    cacheTags: expect.anything(),
                    cacheSeconds: expect.anything(),
                },
                'https://api.example.com',
            );

            expect(result).toEqual({
                ok: true,
            });

            spy.mockRestore();
        },
    );

    it(
        'delegates to MakeWithToken with correct arguments',
        async () => {
            const spy = vi.spyOn(
                MakeWithTokenModule,
                'MakeWithToken',
            ).mockResolvedValue(
                // @ts-expect-error TS2345
                {
                    ok: true,
                },
            );

            // @ts-expect-error TS2345
            const factory = RequestFactory(baseProps);

            const props = {
                uri: '/bar',
            };

            const result = await factory.makeWithToken(props);

            expect(spy).toHaveBeenCalledWith(
                {
                    uri: '/bar',
                    method: expect.anything(),
                    queryParams: expect.anything(),
                    payload: expect.anything(),
                    cacheTags: expect.anything(),
                    cacheSeconds: expect.anything(),
                },
                'https://api.example.com',
                'next-auth',
                mockTokenRepository,
                mockRefreshAccessToken,
            );

            expect(result).toEqual({
                ok: true,
            });

            spy.mockRestore();
        },
    );

    it(
        'delegates to MakeWithSignInRedirect with correct arguments',
        async () => {
            const spy = vi.spyOn(
                MakeWithSignInRedirectModule,
                'MakeWithSignInRedirect',
            ).mockResolvedValue(
                // @ts-expect-error TS2345
                {
                    ok: true,
                },
            );

            // @ts-expect-error TS2345
            const factory = RequestFactory(baseProps);

            const props = {
                uri: '/baz',
            };

            const result = await factory.makeWithSignInRedirect(props);

            expect(spy).toHaveBeenCalledWith(
                {
                    uri: '/baz',
                    method: expect.anything(),
                    queryParams: expect.anything(),
                    payload: expect.anything(),
                    cacheTags: expect.anything(),
                    cacheSeconds: expect.anything(),
                },
                'https://app.example.com',
                'https://api.example.com',
                'next-auth',
                mockTokenRepository,
                mockRefreshAccessToken,
                '/api/auth/sign-in',
            );

            expect(result).toEqual({
                ok: true,
            });

            spy.mockRestore();
        },
    );
});
