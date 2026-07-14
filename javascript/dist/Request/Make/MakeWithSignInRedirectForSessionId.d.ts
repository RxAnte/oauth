import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { PagesRequest } from '../../TokenRepository/OAuthSessionIdCookie';
import { RefreshAccessTokenForSessionId } from '../RefreshAccessToken/RefreshAccessTokenForSessionId';
import { PagesRouterRedirect } from '../RequestForSessionId';
export declare function MakeWithSignInRedirectForSessionId(props: RequestProperties, req: PagesRequest, requestBaseUrl: string, 
/** @deprecated RxAnte Oauth is moving away from next-auth. */
nextAuthProviderId: string | undefined, tokenRepository: TokenRepository, refreshAccessToken: RefreshAccessTokenForSessionId, signInUri?: string): Promise<RequestResponse | PagesRouterRedirect>;
