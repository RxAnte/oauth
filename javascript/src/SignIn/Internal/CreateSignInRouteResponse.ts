import { cookies } from 'next/headers';
import { randomBytes } from 'crypto';

// TODO: Refactor this to be less procedural
export default async function CreateSignInRouteResponse (
    request: Request,
    appUrl: string,
    authorizeUrl: string,
    clientId: string,
    callbackUri: string = '/api/auth/callback',
): Promise<Response> {
    const { searchParams } = new URL(request.url);

    // TODO: Validate that this URL domain matches the appUrl
    const authReturn = searchParams.get('authReturn') || appUrl;

    const cookieStore = await cookies();

    const authorizeState = randomBytes(32).toString('hex');

    cookieStore.set('authReturn', authReturn, {
        httpOnly: true,
        path: '/',
        maxAge: 60 * 10, // Ten minutes
        secure: true,
    });

    cookieStore.set('authorizeState', authorizeState, {
        httpOnly: true,
        path: '/',
        maxAge: 60 * 10, // Ten minutes
        secure: true,
    });

    let callbackUrl = appUrl.endsWith('/')
        ? appUrl.slice(0, -1)
        : appUrl;

    callbackUrl += callbackUri;

    const authorizeUri = new URL(authorizeUrl);

    authorizeUri.searchParams.append('response_type', 'code');

    authorizeUri.searchParams.append('client_id', clientId);

    authorizeUri.searchParams.append('redirect_uri', callbackUrl);

    authorizeUri.searchParams.append(
        'scope',
        'openid profile email offline_access',
    );

    authorizeUri.searchParams.append('state', authorizeState);

    return Response.redirect(authorizeUri.toString(), 302);
}
