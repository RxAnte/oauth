import { RequestResponse } from '../RequestResponse';
import { RequestProperties } from '../RequestProperties';
import { TokenRepository } from '../../TokenRepository/TokenRepository';
import { RefreshAccessToken } from '../RefreshAccessToken/RefreshAccessToken';
export declare function MakeWithToken(props: RequestProperties, requestBaseUrl: string, nextAuthProviderId: string, tokenRepository: TokenRepository, refreshAccessToken: RefreshAccessToken): Promise<RequestResponse>;
