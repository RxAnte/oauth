import Redis from 'ioredis';
import { TokenData } from '../../TokenData';
export declare function FindTokenBySessionId(sessionId: string, redis: Redis): Promise<TokenData | null>;
