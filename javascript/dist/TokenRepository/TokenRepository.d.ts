import { Account } from 'next-auth';
import { User } from '../NextAuth/User';
export type TokenRepository = {
    createSessionIdWithAccessToken: (token: Account, user: User) => Promise<string>;
};
