export type RefreshLock = {
    acquire: (accessToken: string) => Promise<void>;
    release: (accessToken: string) => Promise<void>;
};
