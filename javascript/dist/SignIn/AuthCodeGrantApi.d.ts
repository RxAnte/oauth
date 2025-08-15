export type AuthCodeGrantApi = {
    createSignInRouteResponse: (request: Request) => Promise<Response>;
    respondToAuthCodeCallback: (request: Request) => Promise<Response>;
};
