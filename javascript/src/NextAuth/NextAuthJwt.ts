import { z } from 'zod';
import { JWT } from 'next-auth/jwt';
import { User, UserSchema } from './User';

export const NextAuthJwtSchema = z.object({
    accessToken: z.string(),
    accessTokenExpires: z.number(),
    refreshToken: z.string(),
    user: UserSchema,
});

export interface NextAuthJwt extends JWT {
    accessToken: string;
    accessTokenExpires: number;
    refreshToken: string;
    user: User;
}
