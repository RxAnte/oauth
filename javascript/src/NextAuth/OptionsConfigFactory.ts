import { Account, AuthOptions } from 'next-auth';
import { Provider } from 'next-auth/providers';
import { JWT } from 'next-auth/jwt';
import { User } from './User';
import { TokenRepository } from '../TokenRepository/TokenRepository';

export function OptionsConfigFactory (
    {
        secret,
        providers,
        tokenRepository,
        debug = false,
    }: {
        secret: string;
        providers: Array<Provider>;
        tokenRepository: TokenRepository;
        debug?: boolean;
    },
): AuthOptions {
    return {
        secret,
        providers,
        debug,
        callbacks: {
            // eslint-disable-next-line @typescript-eslint/ban-ts-comment
            // @ts-ignore
            jwt: async (
                {
                    token,
                    user,
                    account,
                }: {
                    token: JWT;
                    user: User;
                    account: Account;
                },
            ) => {
                // Initial sign in
                if (account && user) {
                    const sessionId = await tokenRepository.createSessionIdWithToken(
                        account,
                        user,
                    );

                    return { sessionId };
                }

                return token;
            },
        },
    };
}
