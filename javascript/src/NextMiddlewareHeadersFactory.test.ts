import { describe, it, expect } from 'vitest';
import { NextMiddlewareHeadersFactory } from './NextMiddlewareHeadersFactory';

function makeMockRequest (
    {
        url,
        headers = {},
        pathname = '/',
        searchParams = '',
    }: {
        url: string;
        headers?: Record<string, string>;
        pathname?: string;
        searchParams?: string;
    },
) {
    return {
        url,
        headers: new Headers(headers),
        nextUrl: {
            pathname,
            searchParams: new URLSearchParams(searchParams),
        },
    };
}

describe('NextMiddlewareHeadersFactory', () => {
    it('should return original headers when url includes "_next"', () => {
        const req = makeMockRequest({
            url: 'https://example.com/_next/static/chunk.js',
            headers: { 'x-test': '123' },
        });

        const result = NextMiddlewareHeadersFactory(req as never);

        expect(result.get('x-test')).toBe('123');
        expect(result.get('middleware-pathname')).toBeNull();
        expect(result.get('middleware-search-params')).toBeNull();
    });

    it('should set pathname and search params when url does not include "_next"', () => {
        const req = makeMockRequest({
            url: 'https://example.com/dashboard?foo=bar&baz=qux',
            headers: { 'x-test': 'abc' },
            pathname: '/dashboard',
            searchParams: 'foo=bar&baz=qux',
        });

        const result = NextMiddlewareHeadersFactory(req as never);

        expect(result.get('x-test')).toBe('abc');
        expect(result.get('middleware-pathname')).toBe('/dashboard');
        expect(result.get('middleware-search-params')).toBe('foo=bar&baz=qux');
    });

    it('should handle requests with no search params', () => {
        const req = makeMockRequest({
            url: 'https://example.com/profile',
            pathname: '/profile',
            searchParams: '',
        });

        const result = NextMiddlewareHeadersFactory(req as never);

        expect(result.get('middleware-pathname')).toBe('/profile');
        expect(result.get('middleware-search-params')).toBe('');
    });
});
