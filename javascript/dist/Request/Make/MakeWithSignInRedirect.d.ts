import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshAccessToken } from '../RefreshAccessToken/RefreshAccessToken';
export declare function MakeWithSignInRedirect(props: RequestProperties, appUrl: string, requestBaseUrl: string, 
/** @deprecated RxAnte Oauth is moving away from next-auth. */
nextAuthProviderId: string | undefined, tokenRepository: TokenRepository, refreshAccessToken: RefreshAccessToken, signInUri?: string): Promise<RequestResponse>;
