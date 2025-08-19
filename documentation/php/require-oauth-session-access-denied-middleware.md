# RequireOauthSessionAccessDeniedMiddleware

`\RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware`

A [PSR-15](https://github.com/php-fig/http-server-middleware) server middleware implementation.

This middleware ensures a valid token to be present in a user's session. If the token stored in the session is valid, a `\RxAnte\OAuth\UserInfo\OauthUserInfo` instance will be added to the request attributes with the name `oauthUserInfo`. If there is no valid session, an access denied page will be shown.

You can also implement an instance of `\RxAnte\OAuth\CustomAuthenticationHook` to do any custom authentication your application needs, and/or create a custom `\Psr\Http\Message\ResponseInterface` to respond with. See the [Custom Authentication Hook documentation](custom-auth-hook.md) for more details.

Additionally, you can implement an instance of `\RxAnte\OAuth\CustomResponseFactory` to use in the `\RxAnte\OAuth\AccessDeniedResponseFactory` (configure in your PSR-11 container configuration) to create a custom access denied page — which you will almost certainly want to do since the default is just the text "Access Denied".

> [!NOTE]
> `RequireOauthSessionAccessDeniedMiddleware` requires an implementation of [OauthUserInfoRepositoryInterface](oauth-user-info-repository-interface.md). This package provides an implementation which will need to be configured in order to use `\RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository`.

> [!NOTE]
> `RequireOauthSessionAccessDeniedMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken`. This package provides an implementation which must be configured: `\RxAnte\OAuth\Handlers\RxAnte\TokenRefresh\RxAnteGetRefreshedAccessToken`.

> [!NOTE]
> `RequireOauthSessionAccessDeniedMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock`. This package provides a Redis implementation. See [Redis Refresh Lock](redis-refresh-lock.md).

With this middleware, the user will never be prompted to go through the oauth flow, you'll be responsible for that elsewhere. You can use `\RxAnte\OAuth\SendToLoginResponseFactory::create` to create a response. Note that when using `\RxAnte\OAuth\SendToLoginResponseFactory::create`, when the oauth login flow is completed, the user will be sent back to the URL from request object's `\Psr\Http\Message\UriInterface`.

> [!NOTE]
> `SendToLoginResponseFactory` flow requires an implementation of `League\OAuth2\Client\Provider\AbstractProvider`. You can learn how to implement that configuration [here](configuring-league-client.md).

> [!NOTE]
> `SendToLoginResponseFactory` flow requires an implementation of `Psr\Clock\ClockInterface` to be provided. [lcobucci/clock](https://github.com/lcobucci/clock) is a good one.

> [!NOTE]
> `SendToLoginResponseFactory` flow requires the PSR-11 container to be able to provide an implementation of `Ramsey\Uuid\UuidFactoryInterface`. You can simply configure your container to serve the default implementation `\Ramsey\Uuid\UuidFactory`

See other requirements about `TokenRepositoryConfig` [here](require-oauth-session-login-redirect-middleware.md).
