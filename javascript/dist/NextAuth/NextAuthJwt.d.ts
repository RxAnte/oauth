import { z } from 'zod';
import { JWT } from 'next-auth/jwt';
import { User } from './User';
export declare const NextAuthJwtSchema: z.ZodObject<{
    accessToken: z.ZodString;
    accessTokenExpires: z.ZodNumber;
    refreshToken: z.ZodString;
    user: z.ZodObject<{
        id: z.ZodString;
        sub: z.ZodString;
        email: z.ZodString;
        name: z.ZodString;
    }, "strip", z.ZodTypeAny, {
        id: string;
        sub: string;
        email: string;
        name: string;
    }, {
        id: string;
        sub: string;
        email: string;
        name: string;
    }>;
}, "strip", z.ZodTypeAny, {
    accessToken: string;
    accessTokenExpires: number;
    refreshToken: string;
    user: {
        id: string;
        sub: string;
        email: string;
        name: string;
    };
}, {
    accessToken: string;
    accessTokenExpires: number;
    refreshToken: string;
    user: {
        id: string;
        sub: string;
        email: string;
        name: string;
    };
}>;
export interface NextAuthJwt extends JWT {
    accessToken: string;
    accessTokenExpires: number;
    refreshToken: string;
    user: User;
}
