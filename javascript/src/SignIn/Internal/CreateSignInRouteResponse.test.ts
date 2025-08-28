import {
    vi, describe, it, expect, beforeEach,
} from 'vitest';
import { cookies } from 'next/headers';
import CreateSignInRouteResponse from './CreateSignInRouteResponse';

vi.mock('next/headers', () => ({
    cookies: vi.fn(),
}));

vi.mock('crypto', () => ({
    randomBytes: vi.fn(() => ({
        toString: vi.fn(() => 'mock-random-bytes'),
    })),
}));

describe('CreateSignInRouteResponse', () => {
    const mockCookies = {
        set: vi.fn(),
    };

    beforeEach(() => {
        vi.resetAllMocks();

        // @ts-expect-error TS2345
        vi.mocked(cookies).mockReturnValue(mockCookies);
    });

    it('sets authReturn and authorizeState cookies', async () => {
        const response = await CreateSignInRouteResponse(
            new Request(
                'http://localhost?authReturn=http://localhost/return',
            ),
            'http://localhost',
            'https://auth.com/authorize',
            'mock-client-id',
        );

        expect(mockCookies.set).toHaveBeenCalledWith(
            'authReturn',
            'http://localhost/return',
            expect.objectContaining({
                httpOnly: true,
                path: '/',
                maxAge: 600,
                secure: true,
            }),
        );

        expect(mockCookies.set).toHaveBeenCalledWith(
            'authorizeState',
            'mock-random-bytes',
            expect.objectContaining({
                httpOnly: true,
                path: '/',
                maxAge: 600,
                secure: true,
            }),
        );

        expect(response.status).toBe(302);

        expect(response.headers.get('location')).toBe(
            'https://auth.com/authorize?response_type=code&client_id=mock-client-id&redirect_uri=http%3A%2F%2Flocalhost%2Fapi%2Fauth%2Fcallback&scope=openid+profile+email+offline_access&state=mock-random-bytes',
        );
    });

    it('uses modifyAuthorizeUrl properly if present', async () => {
        const response = await CreateSignInRouteResponse(
            new Request(
                'http://localhost?authReturn=http://malicioushost/return',
            ),
            'http://localhost/',
            'https://auth.com/authorize',
            'mock-client-id',
            '/foo/callback',
            'test-audience',
            (url) => {
                url.searchParams.append('foo', 'bar');
            },
        );

        expect(mockCookies.set).toHaveBeenCalledWith(
            'authReturn',
            'http://localhost/',
            expect.objectContaining({
                httpOnly: true,
                path: '/',
                maxAge: 600,
                secure: true,
            }),
        );

        expect(mockCookies.set).toHaveBeenCalledWith(
            'authorizeState',
            'mock-random-bytes',
            expect.objectContaining({
                httpOnly: true,
                path: '/',
                maxAge: 600,
                secure: true,
            }),
        );

        expect(response.status).toBe(302);

        expect(response.headers.get('location')).toBe(
            'https://auth.com/authorize?response_type=code&client_id=mock-client-id&redirect_uri=http%3A%2F%2Flocalhost%2Ffoo%2Fcallback&scope=openid+profile+email+offline_access&state=mock-random-bytes&audience=test-audience&foo=bar',
        );
    });
});
