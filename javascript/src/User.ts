import { z } from 'zod';

export const UserSchema = z.object({
    id: z.string(),
    sub: z.string(),
    email: z.string(),
    name: z.string(),
});

export interface User {
    id: string;
    sub: string;
    email: string;
    name: string;
}
