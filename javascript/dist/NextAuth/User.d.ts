import { z } from 'zod';
export declare const UserSchema: z.ZodObject<{
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
export interface User {
    id: string;
    sub: string;
    email: string;
    name: string;
}
