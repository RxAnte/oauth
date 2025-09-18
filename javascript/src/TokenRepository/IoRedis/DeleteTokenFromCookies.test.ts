import {
    beforeEach, describe, expect, it, vi,
} from 'vitest';
import Redis from 'ioredis';
import { GetIdFromCookies } from './GetIdFromCookies';
import DeleteTokenFromCookies from './DeleteTokenFromCookies';
import DeleteTokenBySessionId from './DeleteTokenBySessionId';

describe('DeleteTokenFromCookies', () => {
    vi.mock('ioredis');
    vi.mock('./GetIdFromCookies');
    vi.mock('./DeleteTokenBySessionId');

    beforeEach(() => {
        vi.clearAllMocks();
    });

    it(
        'should not call DeleteTokenBySessionId when there is no session id',
        async () => {
            const mockRedis = new Redis();

            vi.mocked(GetIdFromCookies).mockResolvedValue(null);

            await DeleteTokenFromCookies(mockRedis);

            expect(DeleteTokenBySessionId).not.toHaveBeenCalled();
        },
    );

    it(
        'should call DeleteTokenBySessionId when there is a session id',
        async () => {
            const mockRedis = new Redis();

            vi.mocked(GetIdFromCookies).mockResolvedValue('mock-id');

            await DeleteTokenFromCookies(mockRedis);

            expect(DeleteTokenBySessionId).toHaveBeenCalledWith(
                'mock-id',
                mockRedis,
            );
        },
    );
});
