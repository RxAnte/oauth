import Redis from 'ioredis';
import { GetIdFromCookies } from './GetIdFromCookies';
import DeleteTokenBySessionId from './DeleteTokenBySessionId';

export default async function DeleteTokenFromCookies (redis: Redis) {
    const sessionId = await GetIdFromCookies();

    if (!sessionId) {
        return;
    }

    await DeleteTokenBySessionId(sessionId, redis);
}
