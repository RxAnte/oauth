import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshAccessToken } from '../RefreshAccessToken/RefreshAccessToken';
export declare function MakeWithToken(props: RequestProperties, requestBaseUrl: string, 
/** @deprecated RxAnte Oauth is moving away from next-auth. */
nextAuthProviderId: string | undefined, tokenRepository: TokenRepository, refreshAccessToken: RefreshAccessToken): Promise<RequestResponse>;
