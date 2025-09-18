import {
    describe, expect, it, vi,
} from 'vitest';
import { cookies } from 'next/headers';
import DeleteSessionAndCookie from './DeleteSessionAndCookie';
import { TokenRepository } from '../../TokenRepository/TokenRepository';

describe('DeleteSessionAndCookie', () => {
    vi.mock('next/headers', () => ({
        cookies: vi.fn(),
    }));

    it(
        'calls `deleteTokenFromCookies` and cookieStore.delete',
        async () => {
            const mockCookieStore = {
                delete: vi.fn(),
            };

            // @ts-expect-error TS2345
            vi.mocked(cookies).mockReturnValue(mockCookieStore);

            // @ts-expect-error TS2740
            const mockTokenRepository: TokenRepository = {
                deleteTokenFromCookies: vi.fn(),
            };

            await DeleteSessionAndCookie(mockTokenRepository);

            expect(mockTokenRepository.deleteTokenFromCookies).toHaveBeenCalled();

            expect(mockCookieStore.delete).toHaveBeenCalledWith(
                'oauthSessionId',
            );
        },
    );
});
