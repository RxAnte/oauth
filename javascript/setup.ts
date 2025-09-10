import { vi, afterEach, expect } from 'vitest';

const errorSpy = vi.spyOn(console, 'error').mockImplementation(
    () => {
    throw new Error('console.error was called unexpectedly');
});

// This is the recommended way, but doesn't work, so I'm just throwing an error
// above if console.error is called
// afterEach(() => {
//     expect(errorSpy).not.toHaveBeenCalled();
//
//     errorSpy.mockClear();
// });
