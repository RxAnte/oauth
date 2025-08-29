import {
    describe,
    it,
    expect,
    vi,
} from 'vitest';
import { AccessDeniedUserNotLoggedInResponse } from './AccessDeniedResponse';
import RequestAuthenticationError from '../RequestAuthenticationError';
import { RequestResponse } from '../RequestResponse';
import { ParseResponse } from './ParseResponse';

// Mocks
const makeResponse = ({
    status = 200,
    ok = true,
    headers = new Headers(),
    body = null,
    text = '{}',
} = {}) => ({
    status,
    ok,
    headers,
    body,
    text: vi.fn().mockResolvedValue(text),
} as unknown as Response);

describe('ParseResponse', () => {
    it(
        'returns parsed JSON for inline content-disposition',
        async () => {
            const response = makeResponse({
                text: '{"foo":"bar"}',
            });

            const runRequest = vi.fn().mockResolvedValue(response);

            const result = await ParseResponse(runRequest);

            expect(result.json).toEqual({
                foo: 'bar',
            });

            expect(result.ok).toBe(true);

            expect(result.status).toBe(200);
        },
    );

    it(
        'returns empty json for non-inline content-disposition',
        async () => {
            const headers = new Headers();

            headers.set('content-disposition', 'attachment');

            const response = makeResponse({
                headers,
            });

            const runRequest = vi.fn().mockResolvedValue(response);

            const result = await ParseResponse(runRequest);

            expect(result.json).toEqual({});

            expect(result.ok).toBe(true);

            expect(result.status).toBe(200);
        },
    );

    it(
        'returns AccessDeniedUserNotLoggedInResponse for 401',
        async () => {
            const response = makeResponse({
                status: 401,
                ok: false,
                text: 'Unauthorized',
            });

            const runRequest = vi.fn().mockResolvedValue(response);

            const result = await ParseResponse(runRequest);

            expect(result).toEqual(AccessDeniedUserNotLoggedInResponse);
        },
    );

    it(
        'returns error for invalid JSON',
        async () => {
            const response = makeResponse({
                text: 'not json',
            });

            const runRequest = vi.fn().mockResolvedValue(response);

            const result = await ParseResponse(runRequest);

            expect(result.ok).toBe(false);

            expect(result.status).toBe(503);

            if (
                result.json
                    && !Array.isArray(result.json)
                    && typeof result.json === 'object'
            ) {
                expect((result.json as Record<string, unknown>).error).toBe(
                    'invalid_response',
                );
            } else {
                throw new Error(
                    'Expected result.json to be an object with error property',
                );
            }
        },
    );

    it(
        'returns error for thrown RequestAuthenticationError',
        async () => {
            const runRequest = vi.fn().mockRejectedValue(
                new RequestAuthenticationError('fail'),
            );

            const result = await ParseResponse(runRequest);

            expect(result).toEqual(AccessDeniedUserNotLoggedInResponse);
        },
    );

    it(
        'returns generic error for unknown error',
        async () => {
            const runRequest = vi.fn().mockRejectedValue(
                new Error('fail'),
            );

            const result = await ParseResponse(runRequest);

            expect(result.ok).toBe(false);

            expect(result.status).toBe(500);

            if (
                result.json
                    && !Array.isArray(result.json)
                    && typeof result.json === 'object'
            ) {
                expect((result.json as Record<string, unknown>).error).toBe(
                    'unknown_error',
                );
            } else {
                throw new Error(
                    'Expected result.json to be an object with error property',
                );
            }
        },
    );
});
