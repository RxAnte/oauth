import { Account } from 'next-auth';
import { User } from '../User';
import { TokenData } from '../TokenData';

export type TokenRepository = {
    /** @deprecated This was used to support next-auth and is no longer used */
    createSessionIdWithToken: (token: Account, user: User) => Promise<string>;
    findTokenBySessionId: (sessionId: string) => Promise<TokenData | null>;
    findTokenFromCookies: () => Promise<TokenData | null>;
    getTokenFromCookies: () => Promise<TokenData>;
    setTokenFromSessionId: (token: TokenData, id: string) => Promise<void>;
    setTokenBasedOnCookies: (token: TokenData) => Promise<void>;
};
