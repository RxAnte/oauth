import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import { cookies } from 'next/headers';
import { decode } from 'next-auth/jwt';
import { GetIdFromCookies } from './GetIdFromCookies';

vi.mock('next/headers', () => ({
    cookies: vi.fn(),
}));

vi.mock('next-auth/jwt', () => ({
    decode: vi.fn(),
}));

beforeEach(() => {
    vi.clearAllMocks();
});

describe('GetIdFromCookies', () => {
    it(
        'should return the sessionId from the oauthSessionId cookie',
        async () => {
            const mockCookies = {
                get: vi.fn().mockReturnValue({ value: 'session-123' }),
                getAll: vi.fn().mockReturnValue([]),
            };

            // @ts-expect-error TS2345
            vi.mocked(cookies).mockReturnValue(mockCookies);

            const result = await GetIdFromCookies();

            expect(mockCookies.get).toHaveBeenCalledWith('oauthSessionId');

            expect(result).toBe('session-123');
        },
    );

    it(
        'should return null if oauthSessionId cookie exists no oauthSessionId cookie is found and no secret is provided',
        async () => {
            const mockCookies = {
                get: vi.fn().mockReturnValue(null),
                getAll: vi.fn().mockReturnValue([]),
            };

            // @ts-expect-error TS2345
            vi.mocked(cookies).mockReturnValue(mockCookies);

            const result = await GetIdFromCookies();

            expect(mockCookies.get).toHaveBeenCalledWith('oauthSessionId');

            expect(result).toBeNull();
        },
    );

    it(
        'should decode the sessionId from old NextAuth cookies if no oauthSessionId is found',
        async () => {
            const mockCookies = {
                get: vi.fn().mockReturnValue(null),
                getAll: vi.fn().mockReturnValue([{
                    name: '__Secure-next-auth.session-token',
                    value: 'old-token',
                }]),
            };

            // @ts-expect-error TS2345
            vi.mocked(cookies).mockReturnValue(mockCookies);

            vi.mocked(decode).mockResolvedValue({
                sessionId: 'decoded-session-456',
            });

            const result = await GetIdFromCookies('secret-key');

            expect(mockCookies.get).toHaveBeenCalledWith('oauthSessionId');

            expect(mockCookies.getAll).toHaveBeenCalled();

            expect(decode).toHaveBeenCalledWith({
                token: 'old-token',
                secret: 'secret-key',
            });

            expect(result).toBe('decoded-session-456');
        },
    );

    it(
        'should return null if no cookies are found and no secret is provided',
        async () => {
            const mockCookies = {
                get: vi.fn().mockReturnValue(null),
                getAll: vi.fn().mockReturnValue([]),
            };

            // @ts-expect-error TS2345
            vi.mocked(cookies).mockReturnValue(mockCookies);

            const result = await GetIdFromCookies();

            expect(mockCookies.get).toHaveBeenCalledWith('oauthSessionId');

            expect(result).toBeNull();
        },
    );

    it(
        'should return null if no session token cookies are found',
        async () => {
            const mockCookies = {
                get: vi.fn().mockReturnValue(null),
                getAll: vi.fn().mockReturnValue([]),
            };

            // @ts-expect-error TS2345
            vi.mocked(cookies).mockReturnValue(mockCookies);

            const result = await GetIdFromCookies('secret-key');

            expect(mockCookies.get).toHaveBeenCalledWith('oauthSessionId');

            expect(mockCookies.getAll).toHaveBeenCalled();

            expect(result).toBeNull();
        },
    );
});
