export const OAUTH_SESSION_ID_COOKIE = 'oauthSessionId';

export function SessionIdFromPagesRequest (
    { cookies }: { cookies: Partial<Record<string, string>> },
): string | null {
    return cookies[OAUTH_SESSION_ID_COOKIE] ?? null;
}
