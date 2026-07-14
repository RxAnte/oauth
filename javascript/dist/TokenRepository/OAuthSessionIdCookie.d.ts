export declare const OAUTH_SESSION_ID_COOKIE = "oauthSessionId";
export declare function SessionIdFromPagesRequest({ cookies }: {
    cookies: Partial<Record<string, string>>;
}): string | null;
