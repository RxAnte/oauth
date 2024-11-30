import { AuthOptions } from 'next-auth';
import { Provider } from 'next-auth/providers';
import { TokenRepository } from '../TokenRepository/TokenRepository';
export declare function OptionsConfigFactory({ secret, providers, tokenRepository, debug, }: {
    secret: string;
    providers: Array<Provider>;
    tokenRepository: TokenRepository;
    debug?: boolean;
}): AuthOptions;
