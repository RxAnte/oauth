import { Account } from 'next-auth';
import { User } from '../NextAuth/User';
import { NextAuthJwt } from '../NextAuth/NextAuthJwt';

export type TokenRepository = {
    createSessionIdWithToken: (token: Account, user: User) => Promise<string>;
    findTokenBySessionId: (sessionId: string) => Promise<NextAuthJwt | null>;
    findTokenFromCookies: () => Promise<NextAuthJwt | null>;
    getTokenFromCookies: () => Promise<NextAuthJwt>;
    setTokenFromSessionId: (token: NextAuthJwt, id: string) => Promise<void>;
    setTokenBasedOnCookies: (token: NextAuthJwt) => Promise<void>;
};
