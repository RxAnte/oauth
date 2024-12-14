# RequireOauthSessionAccessDeniedMiddleware

`\RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware`

A [PSR-15](https://github.com/php-fig/http-server-middleware) server middleware implementation.

This middleware can be used to require a valid token to be present in a user's session. If the token stored in the session is valid, a `\RxAnte\OAuth\UserInfo\OauthUserInfo` instance will be added to the request attributes with the name `oauthUserInfo`. If there is no valid session, an access denied page will be shown.

This is great for use with browser PHP application access.

> [!NOTE]
> `RequireOauthSessionAccessDeniedMiddleware` requires an implementation of [OauthUserInfoRepositoryInterface](oauth-user-info-repository-interface.md). This package provides an Auth0 implementation or a FusionAuth implementation.

> [!NOTE]
> `RequireOauthSessionAccessDeniedMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken`. This package provides an Auth0 implementation or a FusionAuth implementation. See [Using and Configuring the Auth0 Implementation](using-configuring-auth0-implementation.md) and [Using and Configuring the FusionAuth Implementation](using-configuring-fusion-auth-implementation.md).

> [!NOTE]
> `RequireOauthSessionAccessDeniedMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock`. This package provides a Redis implementation. See [Redis Refresh Lock](redis-refresh-lock.md).

With this middleware, the user will never be prompted to go through the oauth flow, you'll be responsible for that elsewhere. You can use `\RxAnte\OAuth\SendToLoginResponseFactory` to create a response. Note that when the oauth login flow is completed, the user will be sent back to the same URL that that is on the requests `\Psr\Http\Message\UriInterface`.

> [!NOTE]
> `SendToLoginResponseFactory` flow requires an implementation of `League\OAuth2\Client\Provider\AbstractProvider`. You can learn how to implement that configuration [here](configuring-league-client.md).

> [!NOTE]
> `SendToLoginResponseFactory` flow requires an implementation of `Psr\Clock\ClockInterface` to be provided. [lcobucci/clock](https://github.com/lcobucci/clock) is a good one.

> [!NOTE]
> `SendToLoginResponseFactory` flow requires the PSR-11 container to be able to provide an implementation of `Ramsey\Uuid\UuidFactoryInterface`. You can simply configure your container to serve the default implementation `\Ramsey\Uuid\UuidFactory`

See other requirements about `TokenRepositoryConfig` [here](require-oauth-session-login-redirect-middleware.md).

## Custom access denied response

The default access denied response is pretty bare bones and you almost certainly don't want to use it. It's just the text "Access Denied". Luckily, you can provide your own. Add an implementation of `\RxAnte\OAuth\CustomResponseFactory` as a constructor argument to the property `customResponseFactory` via your container and you can provide any response you want when access is denied.
