export type AuthCodeGrantApi = {
    createSignInRouteResponse: (request: Request, modifyAuthorizeUrl?: (url: URL) => void) => Promise<Response>;
    respondToAuthCodeCallback: (request: Request) => Promise<Response>;
};
