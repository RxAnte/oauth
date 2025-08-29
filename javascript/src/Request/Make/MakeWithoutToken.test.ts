// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    describe, it, expect, vi, beforeEach, afterEach,
} from 'vitest';
import { MakeWithoutToken } from './MakeWithoutToken';
import { RequestProperties } from '../RequestProperties';
import RequestMethods from '../RequestMethods';

vi.mock('./ParseResponse', () => ({
    ParseResponse: async (fn: any) => fn(),
}));

describe('MakeWithoutToken', () => {
    const baseUrl = 'https://api.example.com';

    let fetchMock: ReturnType<typeof vi.fn>;

    beforeEach(() => {
        fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            status: 200,
            json: async () => ({ data: 'ok' }),
        });

        global.fetch = fetchMock;
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it(
        'calls fetch with correct URL and options for POST',
        async () => {
            const props: RequestProperties = {
                uri: '/test',
                method: RequestMethods.POST,
                queryParams: new URLSearchParams({ foo: 'bar' }),
                payload: { hello: 'world' },
                cacheTags: ['tag1'],
                cacheSeconds: 60,
            };

            const result = await MakeWithoutToken(props, baseUrl);

            expect(fetchMock).toHaveBeenCalledTimes(1);

            const [url, options] = fetchMock.mock.calls[0];

            expect(url.toString()).toBe(
                'https://api.example.com/test?foo=bar',
            );

            expect(options.method).toBe('POST');

            expect(options.headers.get('Content-Type')).toBe(
                'application/json',
            );

            expect(options.body).toBe(JSON.stringify({ hello: 'world' }));

            expect(options.next).toEqual({ tags: ['tag1'], revalidate: 60 });

            expect(result).toEqual({
                ok: true,
                status: 200,
                json: expect.any(Function),
            });
        },
    );

    it('does not set body for GET', async () => {
        const props: RequestProperties = {
            uri: '/test',
            method: RequestMethods.GET,
            queryParams: new URLSearchParams(),
            payload: {},
            cacheTags: [],
            cacheSeconds: 0,
        };

        await MakeWithoutToken(props, baseUrl);

        const [, options] = fetchMock.mock.calls[0];

        expect(options.method).toBe('GET');

        expect(options.body).toBeUndefined();
    });
});
