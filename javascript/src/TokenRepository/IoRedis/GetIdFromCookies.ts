import { cookies } from 'next/headers';
import { decode } from 'next-auth/jwt';

export async function GetIdFromCookies (
    /** @deprecated secret is no longer require unless still using next-auth */
    secret?: string,
): Promise<string | null> {
    // In Next15, `cookies()` must be awaited
    const cookieStore = await cookies();

    const sessionId = cookieStore.get('oauthSessionId');

    if (sessionId) {
        return sessionId.value;
    }

    if (!secret) {
        return null;
    }

    /**
     * Old NextAuth cookies
     */

    let cookie = '';

    const sessionTokenCookies = cookieStore.getAll().filter(
        (cookieObj) => cookieObj.name.startsWith(
            '__Secure-next-auth.session-token',
        ),
    );

    sessionTokenCookies.forEach((cookieObj) => {
        cookie += cookieObj.value;
    });

    if (!cookie) {
        return null;
    }

    const cookieDecoded = await decode({
        token: cookie,
        secret,
    });

    return cookieDecoded?.sessionId as string | null;
}
