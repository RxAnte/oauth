import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshAccessToken } from '../RefreshAccessToken/RefreshAccessToken';
export declare function MakeWithSignInRedirect(props: RequestProperties, appUrl: string, requestBaseUrl: string, nextAuthProviderId: string, tokenRepository: TokenRepository, refreshAccessToken: RefreshAccessToken, signInUri?: string): Promise<RequestResponse>;
