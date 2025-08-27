import { describe, it, expect } from 'vitest';

import * as pkg from './index';

describe('package exports (index.ts)', () => {
    it('should match the expected public API surface', () => {
        const expectedExports = [
            // Deprecated NextAuth exports
            'NextAuthOptionsConfigFactory',
            'NextAuthAuth0ProviderFactory',
            'NextAuthFusionAuthProviderFactory',

            // TokenRepository
            'TokenRepositoryForIoRedisFactory',

            // Request
            'RequestFactory',

            // Refresh
            'RefreshAccessTokenFactory',
            'RefreshAccessTokenWithAuth0Factory',
            'IoRedisRefreshLockFactory',

            // Middleware
            'NextMiddlewareHeadersFactory',

            // Sign-in
            'AuthCodeGrantApiFactory',
            'WellKnownAuthCodeGrantApiFactory',
        ];

        // Compare the actual keys at runtime
        const actualExports = Object.keys(pkg).sort();

        expect(actualExports).toEqual(expectedExports.sort());
    });

    it('should not accidentally remove types', () => {
        // Type-only exports won't exist at runtime,
        // but we can check that they don't sneak into runtime either.
        // @ts-expect-error TS2339
        expect(pkg.TokenRepository).toBeUndefined();
        // @ts-expect-error TS2339
        expect(pkg.RefreshAccessToken).toBeUndefined();
        // @ts-expect-error TS2339
        expect(pkg.RefreshLock).toBeUndefined();
        // @ts-expect-error TS2339
        expect(pkg.AuthCodeGrantApi).toBeUndefined();
    });
});

