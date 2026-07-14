export declare const OAUTH_SESSION_ID_COOKIE = "oauthSessionId";
export type PagesRequest = {
    cookies: Partial<Record<string, string>>;
};
export declare function SessionIdFromPagesRequest({ cookies }: PagesRequest): string | null;
