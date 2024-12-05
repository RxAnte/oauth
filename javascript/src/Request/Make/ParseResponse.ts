import { RequestResponse } from '../RequestResponse';
import { AccessDeniedUserNotLoggedInResponse } from './AccessDeniedResponse';
import RequestAuthenticationError from '../RequestAuthenticationError';

export async function ParseResponse (
    runRequest: () => Promise<Response>,
): Promise<RequestResponse> {
    try {
        const response = await runRequest();

        const responseBody = response.body;

        /**
         * `.text()` can only be used once, second time will throw exception, so
         * we set it to a variable to be used in more places
         */
        const responseText = await response.text();

        /**
         * We're going to try decoding the API JSON response. But we want to
         * catch any errors like if the API didn't return JSON
         */

        let json = {};

        try {
            /**
             * Auth0 returns a string of 'Unauthorized', rather than json,
             * which causes .json() to fail
             */
            if (
                response.status === 401
                || responseText.toLowerCase() === 'unauthorized'
            ) {
                return AccessDeniedUserNotLoggedInResponse;
            }

            /**
             * We can't use apiRes.json() because we used .text() above, and
             * they're mutually exclusive and running .json() now throws
             * an exception
             */
            json = JSON.parse(responseText);
        } catch (innerError) {
            /**
             * If the response code is not a 2xx response, we can pass the
             * response code through. If it is a 2xx response, we don't want to
             * pass that through since the response is not json and was not
             * actually successful
             */
            const status = response.ok ? 503 : response.status;

            const msg = 'The request returned an invalid response';

            return {
                headers: response.headers,
                ok: false,
                status,
                body: responseBody,
                json: {
                    error: 'invalid_response',
                    error_description: msg,
                    message: msg,
                },
            };
        }

        return {
            headers: response.headers,
            ok: response.ok,
            status: response.status,
            body: responseBody,
            json,
        };
    } catch (outerError) {
        /**
         * If this is an authentication error, we can send access denied
         */
        if (outerError instanceof RequestAuthenticationError) {
            return AccessDeniedUserNotLoggedInResponse;
        }

        return {
            headers: new Headers(),
            ok: false,
            status: 500,
            body: null,
            json: {
                error: 'unknown_error',
                error_description: 'An unknown error occurred',
                message: 'An unknown error occurred',
            },
        };
    }
}