// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/destructuring-assignment */
// eslint-disable-next-line import/no-extraneous-dependencies
import Redis from 'ioredis';

export default async function DeleteTokenBySessionId (
    sessionId: string,
    redis: Redis,
) {
    redis.del(`user_token:${sessionId}`);
}
