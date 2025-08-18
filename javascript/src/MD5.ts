import { createHash } from 'node:crypto';

export default function MD5 (content: string) {
    return createHash('md5').update(content).digest('hex');
}
