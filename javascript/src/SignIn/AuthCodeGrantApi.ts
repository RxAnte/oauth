import { TokenData } from '../TokenData';
import { TokenResponseJson, UserInfoJson } from './Internal/RespondToAuthCodeCallback';

export type AuthCodeGrantApi = {
    createSignInRouteResponse: (
        request: Request,
        modifyAuthorizeUrl?: (url: URL) => void
    ) => Promise<Response>;
    respondToAuthCodeCallback: (
        request: Request,
        onBeforeSuccessRedirect?: (params: {
            sessionId: string;
            token: TokenData;
            userInfoJson: UserInfoJson;
            tokenJson: TokenResponseJson;
        }) => void,
    ) => Promise<Response>;
};
