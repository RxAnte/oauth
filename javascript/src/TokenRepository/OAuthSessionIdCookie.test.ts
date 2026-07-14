import { describe, it, expect } from 'vitest';
import { OAUTH_SESSION_ID_COOKIE, SessionIdFromPagesRequest } from './OAuthSessionIdCookie';

describe('SessionIdFromPagesRequest', () => {
    it(
        'returns the session id from the oauthSessionId cookie',
        () => {
            const req = {
                cookies: { [OAUTH_SESSION_ID_COOKIE]: 'session-123' },
            };

            const result = SessionIdFromPagesRequest(req);

            expect(result).toBe('session-123');
        },
    );

    it(
        'returns null if the oauthSessionId cookie is not present',
        () => {
            const req = { cookies: {} };

            const result = SessionIdFromPagesRequest(req);

            expect(result).toBeNull();
        },
    );
});
