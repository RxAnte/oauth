import { cookies } from 'next/headers';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { OAUTH_SESSION_ID_COOKIE } from '../../TokenRepository/OAuthSessionIdCookie';

export default async function DeleteSessionAndCookie (
    tokenRepository: TokenRepository,
): Promise<void> {
    await tokenRepository.deleteTokenFromCookies();

    const cookieStore = await cookies();

    cookieStore.delete(OAUTH_SESSION_ID_COOKIE);
}
