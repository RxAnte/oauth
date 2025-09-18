import Redis from 'ioredis';
export default function DeleteTokenBySessionId(sessionId: string, redis: Redis): Promise<void>;
