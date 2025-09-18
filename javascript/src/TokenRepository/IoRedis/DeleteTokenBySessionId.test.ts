import {
    describe, expect, it, vi,
} from 'vitest';
import Redis from 'ioredis';
import DeleteTokenBySessionId from './DeleteTokenBySessionId';

describe('DeleteTokenBySessionId', () => {
    it('should call delete on redis with appropriate key', async () => {
        const mockRedis = {
            del: vi.fn(),
        } as unknown as Redis;

        await DeleteTokenBySessionId('mock-id', mockRedis);

        expect(mockRedis.del).toHaveBeenCalledWith('user_token:mock-id');
    });
});
