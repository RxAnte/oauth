import { describe, it, expect, expectTypeOf } from 'vitest';
import { z } from 'zod';
import { UserSchema, User } from './User';

describe('UserSchema', () => {
    it('should validate a correct user object', () => {
        const validUser = {
            id: '123',
            sub: 'sub-abc',
            email: 'test@example.com',
            name: 'John Doe',
        };

        const result = UserSchema.safeParse(validUser);

        expect(result.success).toBe(true);

        expect(result.data).toEqual(validUser);
    });

    it('should fail when a required field is missing', () => {
        const invalidUser = {
            id: '123',
            sub: 'sub-abc',
            email: 'test@example.com',
            // missing name
        };

        const result = UserSchema.safeParse(invalidUser);

        expect(result.success).toBe(false);

        expect(result.error.issues[0].path).toContain('name');
    });

    it('should fail when a field has the wrong type', () => {
        const invalidUser = {
            id: 123, // should be string
            sub: 'sub-abc',
            email: 'test@example.com',
            name: 'John Doe',
        };

        const result = UserSchema.safeParse(invalidUser);

        expect(result.success).toBe(false);

        expect(result.error.issues[0].path).toContain('id');
    });

    it('should fail when email is not a string', () => {
        const invalidUser = {
            id: '123',
            sub: 'sub-abc',
            email: 42, // invalid type
            name: 'John Doe',
        };

        const result = UserSchema.safeParse(invalidUser);

        expect(result.success).toBe(false);

        expect(result.error.issues[0].path).toContain('email');
    });
});

describe('UserSchema types', () => {
    it('User interface should match z.infer<typeof UserSchema>', () => {
        type InferredUser = z.infer<typeof UserSchema>;

        // Vitest provides expectTypeOf to assert TS types
        // @ts-expect-error TS2344
        expectTypeOf<User>().toEqualTypeOf<InferredUser>();
    });
});
