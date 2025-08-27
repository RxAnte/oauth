// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/no-explicit-any */
import {
    describe, it, expect, vi, beforeEach,
} from 'vitest';
import Redis from 'ioredis';
import { GetWellKnown } from './WellKnown';

// Mock dependencies
vi.mock('ioredis');
vi.mock('../src/MD5', () => ({
    default: vi.fn((str) => `md5-${str}`),
}));

// Mock fetch
global.fetch = vi.fn();

describe('GetWellKnown', () => {
    const mockWellKnownUrl = 'https://example.com/.well-known/openid-configuration';
    const mockRedisInstance = {} as Redis;
    const mockWellKnownResponse = {
        authorization_endpoint: 'https://example.com/oauth/authorize',
        token_endpoint: 'https://example.com/oauth/token',
        userinfo_endpoint: 'https://example.com/oauth/userinfo',
        other_field: 'should not be included',
    };

    beforeEach(() => {
        vi.resetAllMocks();

        // Setup redis mock methods
        mockRedisInstance.get = vi.fn();
        mockRedisInstance.set = vi.fn();

        // Setup fetch mock
        (global.fetch as any).mockResolvedValue({
            json: () => Promise.resolve(mockWellKnownResponse),
        });
    });

    it('fetches and transforms well-known data when not in cache', async () => {
        // Setup redis to return null (cache miss)
        // @ts-expect-error TS2339
        mockRedisInstance.get.mockResolvedValue(null);

        const result = await GetWellKnown(mockWellKnownUrl, mockRedisInstance);

        // Verify fetch was called
        expect(global.fetch).toHaveBeenCalledWith(mockWellKnownUrl);

        // Verify redis operations
        expect(mockRedisInstance.get).toHaveBeenCalledWith('rxante_oauth_well_known_md5-https://example.com/.well-known/openid-configuration');
        expect(mockRedisInstance.set).toHaveBeenCalledWith(
            'rxante_oauth_well_known_md5-https://example.com/.well-known/openid-configuration',
            JSON.stringify({
                authorizationEndpoint: mockWellKnownResponse.authorization_endpoint,
                tokenEndpoint: mockWellKnownResponse.token_endpoint,
                userinfoEndpoint: mockWellKnownResponse.userinfo_endpoint,
            }),
            'EX',
            86400,
        );

        // Verify returned data structure
        expect(result).toEqual({
            authorizationEndpoint: mockWellKnownResponse.authorization_endpoint,
            tokenEndpoint: mockWellKnownResponse.token_endpoint,
            userinfoEndpoint: mockWellKnownResponse.userinfo_endpoint,
        });
    });

    it('returns cached data when available', async () => {
        const cachedData = {
            authorizationEndpoint: 'cached-auth-endpoint',
            tokenEndpoint: 'cached-token-endpoint',
            userinfoEndpoint: 'cached-userinfo-endpoint',
        };

        // Setup redis to return cached data
        // @ts-expect-error TS2339
        mockRedisInstance.get.mockResolvedValue(JSON.stringify(cachedData));

        const result = await GetWellKnown(mockWellKnownUrl, mockRedisInstance);

        // Verify fetch was not called
        expect(global.fetch).not.toHaveBeenCalled();

        // Verify redis get was called
        expect(mockRedisInstance.get).toHaveBeenCalledWith('rxante_oauth_well_known_md5-https://example.com/.well-known/openid-configuration');

        // Verify redis set was not called
        expect(mockRedisInstance.set).not.toHaveBeenCalled();

        // Verify returned data matches cache
        expect(result).toEqual(cachedData);
    });

    it('works without redis', async () => {
        const result = await GetWellKnown(mockWellKnownUrl);

        // Verify fetch was called
        expect(global.fetch).toHaveBeenCalledWith(mockWellKnownUrl);

        // Verify result
        expect(result).toEqual({
            authorizationEndpoint: mockWellKnownResponse.authorization_endpoint,
            tokenEndpoint: mockWellKnownResponse.token_endpoint,
            userinfoEndpoint: mockWellKnownResponse.userinfo_endpoint,
        });
    });
});
