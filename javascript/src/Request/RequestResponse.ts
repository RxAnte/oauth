export type RequestResponse = {
    readonly headers: Headers;
    readonly ok: boolean;
    readonly status: number;
    readonly body: ReadableStream<Uint8Array> | null;
    readonly json?: Array<unknown> | Record<string, unknown>;
};
