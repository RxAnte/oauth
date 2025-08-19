# RequireOauthSessionLoginRedirectMiddleware

`\RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware`

A [PSR-15](https://github.com/php-fig/http-server-middleware) server middleware implementation.

This middleware ensures a valid token is present in a user's session. If the token stored in the session is valid, a `\RxAnte\OAuth\UserInfo\OauthUserInfo` instance will be added to the request attributes with the name `oauthUserInfo`. If there is no valid session, the user will be sent through the OAuth process to acquire a token to store on the session.

You can also implement an instance of `\RxAnte\OAuth\CustomAuthenticationHook` to do any custom authentication your application needs, and/or create a custom `\Psr\Http\Message\ResponseInterface` to respond with. See the [Custom Authentication Hook documentation](custom-auth-hook.md) for more details.

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of [OauthUserInfoRepositoryInterface](oauth-user-info-repository-interface.md). This package provides an implementation which will need to be configured in order to use `\RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository`.

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken`. This package provides an implementation which must be configured: `\RxAnte\OAuth\Handlers\RxAnte\TokenRefresh\RxAnteGetRefreshedAccessToken`.

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock`. This package provides a Redis implementation. See [Redis Refresh Lock](redis-refresh-lock.md).

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of `League\OAuth2\Client\Provider\AbstractProvider`. You can learn how to implement that configuration [here](configuring-league-client.md).

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires the PSR-11 container to be able to provide an implementation of `Psr\Clock\ClockInterface`. [lcobucci/clock](https://github.com/lcobucci/clock) is a good one.

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires the PSR-11 container to be able to provide an implementation of `Ramsey\Uuid\UuidFactoryInterface`. You can configure your container to serve the default implementation `\Ramsey\Uuid\UuidFactory`

## Configuring

`\RxAnte\OAuth\TokenRepository\TokenRepositoryConfig` must be set up and provided through your [PSR-11](https://www.php-fig.org/psr/psr-11/) container.

[PHP-DI](https://php-di.org) example

```php
use DI\ContainerBuilder;
use RxAnte\OAuth\TokenRepository\TokenRepositoryConfig

use function DI\get as resolveFromContainer;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        TokenRepositoryConfig::class => static function (): TokenRepositoryConfig {
            return new TokenRepositoryConfig(
                expireInSeconds: 4800,
                cacheKeyPrefix: 'SOME_KEY_PREFIX-', // not required, default is session_id_user_token-
            );
        },
    ])
```

## Routing

To use this middleware, add it to any route that needs to be protected.

[Slim 4](https://www.slimframework.com) example

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use Slim\Factory\AppFactory;

// ...you'll need to set up your dependency injection, this example does
// not include that

$app = AppFactory::create();

$app->get('/some/route', static function (
    ServerRequestInterface $request,
    ResponseInterface $response,
): ResponseInterface {
    $userInfo = $request->getAttribute('oauthUserInfo');
    assert($userInfo instanceof OauthUserInfo);

    $response->getBody()->write('You are authenticated as ' . $userInfo->name);

    return $response;
})->add(RequireOauthSessionLoginRedirectMiddleware::class);
```
