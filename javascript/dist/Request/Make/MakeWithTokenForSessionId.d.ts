import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshAccessTokenForSessionId } from '../RefreshAccessToken/RefreshAccessTokenForSessionId';
export declare function MakeWithTokenForSessionId(props: RequestProperties, requestBaseUrl: string, 
/** @deprecated RxAnte Oauth is moving away from next-auth. */
nextAuthProviderId: string | undefined, tokenRepository: TokenRepository, refreshAccessToken: RefreshAccessTokenForSessionId, sessionId: string): Promise<RequestResponse>;
