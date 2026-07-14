export const OAUTH_SESSION_ID_COOKIE = 'oauthSessionId';

export type PagesRequest = {
    cookies: Partial<Record<string, string>>;
};

export function SessionIdFromPagesRequest (
    { cookies }: PagesRequest,
): string | null {
    return cookies[OAUTH_SESSION_ID_COOKIE] ?? null;
}
