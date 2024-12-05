import { cookies } from 'next/headers';
import { decode } from 'next-auth/jwt';

export async function GetIdFromCookies (secret: string): Promise<string | null> {
    const cookie = cookies().get('__Secure-next-auth.session-token');

    if (!cookie) {
        return null;
    }

    const cookieDecoded = await decode({
        token: cookie.value,
        secret,
    });

    return cookieDecoded?.sessionId as string | null;
}
