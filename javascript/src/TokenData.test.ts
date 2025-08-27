import { describe, it, expect, expectTypeOf } from 'vitest';
import { z } from 'zod';
import { TokenDataSchema, TokenData } from './TokenData';
import { User } from './User';

describe('TokenDataSchema', () => {
    it('should validate a correct TokenData object', () => {
        const validTokenData = {
            accessToken: 'abc123',
            accessTokenExpires: 1735689600,
            refreshToken: 'refresh123',
            user: {
                id: '1',
                sub: 'sub-1',
                email: 'test@example.com',
                name: 'Jane Doe',
            },
        };

        const result = TokenDataSchema.safeParse(validTokenData);

        expect(result.success).toBe(true);

        expect(result.data).toEqual(validTokenData);
    });

    it('should fail when a required field is missing', () => {
        const invalidTokenData = {
            accessToken: 'abc123',
            accessTokenExpires: 1735689600,
            // missing refreshToken
            user: {
                id: '1',
                sub: 'sub-1',
                email: 'test@example.com',
                name: 'Jane Doe',
            },
        };

        const result = TokenDataSchema.safeParse(invalidTokenData);

        expect(result.success).toBe(false);

        expect(result.error.issues[0].path).toContain('refreshToken');
    });

    it('should fail when user schema is invalid', () => {
        const invalidTokenData = {
            accessToken: 'abc123',
            accessTokenExpires: 1735689600,
            refreshToken: 'refresh123',
            user: {
                id: 1, // ❌ should be string
                sub: 'sub-1',
                email: 'test@example.com',
                name: 'Jane Doe',
            },
        };

        const result = TokenDataSchema.safeParse(invalidTokenData);

        expect(result.success).toBe(false);

        expect(result.error.issues[0].path).toContain('id');
    });
});

describe('TokenData type consistency', () => {
    it('TokenData interface should match z.infer<typeof TokenDataSchemaSchema>', () => {
        type InferredTokenData = z.infer<typeof TokenDataSchema>;
        // @ts-expect-error TS2344
        expectTypeOf<TokenData>().toEqualTypeOf<InferredTokenData>();
    });

    it('TokenData.user should match User', () => {
        type InferredTokenData = z.infer<typeof TokenDataSchema>;
        expectTypeOf<TokenData['user']>().toEqualTypeOf<User>();
        // @ts-expect-error TS2344
        expectTypeOf<InferredTokenData['user']>().toEqualTypeOf<User>();
    });
});
