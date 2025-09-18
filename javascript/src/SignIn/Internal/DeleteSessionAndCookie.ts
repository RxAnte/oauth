import { cookies } from 'next/headers';
import { TokenRepository } from '../../TokenRepository/TokenRepository';

export default async function DeleteSessionAndCookie (
    tokenRepository: TokenRepository,
): Promise<void> {
    await tokenRepository.deleteTokenFromCookies();

    const cookieStore = await cookies();

    cookieStore.delete('oauthSessionId');
}
