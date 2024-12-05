export const AccessDeniedUserNotLoggedInResponse = {
    headers: new Headers(),
    ok: false,
    status: 401,
    body: null,
    json: {
        error: 'access_denied',
        error_description: 'The user is not logged in',
        message: 'You must log in to access this resource',
    },
};
