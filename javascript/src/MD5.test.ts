import { describe, it, expect } from 'vitest';
import MD5 from './MD5';

describe('MD5', () => {
    it('should return correct MD5 hash for a known string', () => {
        const result = MD5('hello');
        expect(result).toBe('5d41402abc4b2a76b9719d911017c592');
    });

    it('should produce different hashes for different inputs', () => {
        const hash1 = MD5('foo');
        const hash2 = MD5('bar');
        expect(hash1).not.toBe(hash2);
    });

    it('should always return a 32-character hex string', () => {
        const hash = MD5('some random text');
        expect(hash).toMatch(/^[a-f0-9]{32}$/);
    });

    it('should be deterministic for the same input', () => {
        const hash1 = MD5('repeatable');
        const hash2 = MD5('repeatable');
        expect(hash1).toBe(hash2);
    });
});
