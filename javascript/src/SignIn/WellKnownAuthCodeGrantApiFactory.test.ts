import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import Redis from 'ioredis';
import { WellKnownAuthCodeGrantApiFactory } from './WellKnownAuthCodeGrantApiFactory';
import { GetWellKnown, WellKnown } from '../WellKnown';
import { AuthCodeGrantApiFactory } from './AuthCodeGrantApiFactory';
import { TokenRepository } from '../TokenRepository/TokenRepository';
import { AuthCodeGrantApi } from './AuthCodeGrantApi';

vi.mock('../WellKnown', () => ({
    GetWellKnown: vi.fn(),
}));

vi.mock('./AuthCodeGrantApiFactory', () => ({
    AuthCodeGrantApiFactory: vi.fn(),
}));

describe('WellKnownAuthCodeGrantApiFactory', () => {
    let mockTokenRepository: TokenRepository;

    let mockRedis: Redis;

    const wellKnownReturn: WellKnown = {
        authorizationEndpoint: 'https://auth.com/authorize',
        tokenEndpoint: 'https://auth.com/token',
        userinfoEndpoint: 'https://auth.com/userinfo',
    };

    const authCodeGrantApiFactoryReturn: AuthCodeGrantApi = {
        createSignInRouteResponse: async () => new Response(),
        respondToAuthCodeCallback: async () => new Response(),
    };

    beforeEach(() => {
        vi.resetAllMocks();

        mockTokenRepository = {} as TokenRepository;

        mockRedis = {} as Redis;

        vi.mocked(GetWellKnown).mockReturnValue(
            Promise.resolve(wellKnownReturn),
        );

        vi.mocked(AuthCodeGrantApiFactory).mockReturnValue(
            authCodeGrantApiFactoryReturn,
        );
    });

    it(
        'fetches well-known configuration and creates AuthCodeGrantApi',
        async () => {
            const result = await WellKnownAuthCodeGrantApiFactory({
                tokenRepository: mockTokenRepository,
                appUrl: 'https://localhost',
                wellKnownUrl: 'https://auth.com/.well-known',
                clientId: 'mock-client-id',
                clientSecret: 'mock-client-secret',
                callbackUri: '/mock/callback/uri',
                redis: mockRedis,
                wellKnownCacheKey: 'mock-cache-key',
                wellKnownCacheExpiresInSeconds: 123,
                audience: 'mock-audience',
            });

            expect(GetWellKnown).toHaveBeenCalledWith(
                'https://auth.com/.well-known',
                mockRedis,
                'mock-cache-key',
                123,
            );

            expect(AuthCodeGrantApiFactory).toHaveBeenCalledWith({
                tokenRepository: mockTokenRepository,
                appUrl: 'https://localhost',
                authorizeUrl: 'https://auth.com/authorize',
                tokenUrl: 'https://auth.com/token',
                userInfoUrl: 'https://auth.com/userinfo',
                clientId: 'mock-client-id',
                clientSecret: 'mock-client-secret',
                callbackUri: '/mock/callback/uri',
                audience: 'mock-audience',
            });

            expect(result).toBe(authCodeGrantApiFactoryReturn);
        },
    );

    it(
        'uses default values for optional parameters',
        async () => {
            const result = await WellKnownAuthCodeGrantApiFactory({
                tokenRepository: mockTokenRepository,
                appUrl: 'https://appurl',
                wellKnownUrl: 'https://auth.com/.well-known/mock',
                clientId: 'client-id',
                clientSecret: 'client-secret',
            });

            expect(GetWellKnown).toHaveBeenCalledWith(
                'https://auth.com/.well-known/mock',
                undefined,
                'rxante_oauth_well_known',
                86400,
            );

            expect(AuthCodeGrantApiFactory).toHaveBeenCalledWith({
                tokenRepository: mockTokenRepository,
                appUrl: 'https://appurl',
                authorizeUrl: 'https://auth.com/authorize',
                tokenUrl: 'https://auth.com/token',
                userInfoUrl: 'https://auth.com/userinfo',
                clientId: 'client-id',
                clientSecret: 'client-secret',
                callbackUri: '/api/auth/callback',
                audience: undefined,
            });

            expect(result).toBe(authCodeGrantApiFactoryReturn);
        },
    );
});
