import {
    describe,
    it,
    expect,
    vi,
} from 'vitest';

import { RequestForSessionIdFactory } from './RequestForSessionIdFactory';
import * as MakeWithTokenForSessionIdModule from './Make/MakeWithTokenForSessionId';

const mockTokenRepository = {};
const mockRefreshAccessToken = {};

const baseProps = {
    requestBaseUrl: 'https://api.example.com',
    tokenRepository: mockTokenRepository,
    nextAuthProviderId: 'next-auth',
    refreshAccessToken: mockRefreshAccessToken,
};

describe('RequestForSessionIdFactory', () => {
    it(
        'returns an object with the correct methods',
        () => {
            // @ts-expect-error TS2345
            const factory = RequestForSessionIdFactory(baseProps);

            expect(typeof factory.makeWithToken).toBe('function');
        },
    );

    it(
        'delegates to MakeWithTokenForSessionId with correct arguments',
        async () => {
            const spy = vi.spyOn(
                MakeWithTokenForSessionIdModule,
                'MakeWithTokenForSessionId',
            ).mockResolvedValue(
                // @ts-expect-error TS2345
                {
                    ok: true,
                },
            );

            // @ts-expect-error TS2345
            const factory = RequestForSessionIdFactory(baseProps);

            const props = {
                uri: '/bar',
            };

            const result = await factory.makeWithToken('session-123', props);

            expect(spy).toHaveBeenCalledWith(
                {
                    uri: '/bar',
                    method: expect.anything(),
                    queryParams: expect.anything(),
                    payload: expect.anything(),
                    cacheTags: expect.anything(),
                    cacheSeconds: expect.anything(),
                    headers: expect.anything(),
                },
                'https://api.example.com',
                'next-auth',
                mockTokenRepository,
                mockRefreshAccessToken,
                'session-123',
            );

            expect(result).toEqual({
                ok: true,
            });

            spy.mockRestore();
        },
    );
});
