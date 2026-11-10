import Redis from 'ioredis';

export default async function DeleteTokenBySessionId (
    sessionId: string,
    redis: Redis,
) {
    redis.del(`user_token:${sessionId}`);
}
