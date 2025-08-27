import { z } from 'zod';
import { User, UserSchema } from './User';

export const TokenDataSchema = z.object({
    accessToken: z.string(),
    accessTokenExpires: z.number(),
    refreshToken: z.string(),
    user: UserSchema,
});

export interface TokenData {
    accessToken: string;
    accessTokenExpires: number;
    refreshToken: string;
    user: User;
}
