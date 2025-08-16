/**
 * NextAuth
 */

/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
export { OptionsConfigFactory as NextAuthOptionsConfigFactory } from './NextAuth/OptionsConfigFactory';

/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
export { Auth0ProviderFactory as NextAuthAuth0ProviderFactory } from './NextAuth/Auth0ProviderFactory';

/**
 * @deprecated RxAnte Oauth is moving away from next-auth. Use the AuthCodeGrantApi instead
 */
export { FusionAuthProviderFactory as NextAuthFusionAuthProviderFactory } from './NextAuth/FusionAuthProviderFactory';

/**
 * TokenRepository
 */
export type { TokenRepository } from './TokenRepository/TokenRepository';

export { IoRedisTokenRepositoryFactory as TokenRepositoryForIoRedisFactory } from './TokenRepository/IoRedis/IoRedisTokenRepositoryFactory';

/**
 * Request
 */
export { RequestFactory } from './Request/RequestFactory';

/**
 * Refresh
 */

export type { RefreshAccessToken } from './Request/RefreshAccessToken/RefreshAccessToken';

export { RefreshAccessTokenFactory } from './Request/RefreshAccessToken/RefreshAccessTokenFactory';

/** @deprecated */
export { RefreshAccessTokenWithAuth0Factory } from './Request/RefreshAccessToken/RefreshAccessTokenWithAuth0Factory';

export type { RefreshLock } from './Request/RefreshAccessToken/Lock/RefreshLock';

export { IoRedisRefreshLockFactory } from './Request/RefreshAccessToken/Lock/IoRedisRefreshLockFactory';

/**
 * Middleware
 */

export { NextMiddlewareHeadersFactory } from './NextMiddlewareHeadersFactory';

/**
 * Sign-in
 */

export type { AuthCodeGrantApi } from './SignIn/AuthCodeGrantApi';

export { AuthCodeGrantApiFactory } from './SignIn/AuthCodeGrantApiFactory';
