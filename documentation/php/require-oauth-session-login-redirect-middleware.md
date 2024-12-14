# RequireOauthSessionLoginRedirectMiddleware

`\RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware`

A [PSR-15](https://github.com/php-fig/http-server-middleware) server middleware implementation.

This middleware can be used to require a valid token to be present in a user's session. If the token stored in the session is valid, a `\RxAnte\OAuth\UserInfo\OauthUserInfo` instance will be added to the request attributes with the name `oauthUserInfo`. If there is no valid session, the user will be sent through the OAuth process to acquire a token to store on the session.

This is great for use with browser PHP application access.

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of [OauthUserInfoRepositoryInterface](oauth-user-info-repository-interface.md). This package provides an Auth0 implementation or a FusionAuth implementation.

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken`. This package provides an Auth0 implementation or a FusionAuth implementation. See [Using and Configuring the Auth0 Implementation](using-configuring-auth0-implementation.md) and [Using and Configuring the FusionAuth Implementation](using-configuring-fusion-auth-implementation.md).

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock`. This package provides a Redis implementation. See [Redis Refresh Lock](redis-refresh-lock.md).

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of `League\OAuth2\Client\Provider\AbstractProvider`. You can learn how to implement that configuration [here](configuring-league-client.md).

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires an implementation of `Psr\Clock\ClockInterface` to be provided. [lcobucci/clock](https://github.com/lcobucci/clock) is a good one.

> [!NOTE]
> `RequireOauthSessionLoginRedirectMiddleware` requires the PSR-11 container to be able to provide an implementation of `Ramsey\Uuid\UuidFactoryInterface`. You can simply configure your container to serve the default implementation `\Ramsey\Uuid\UuidFactory`

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
use RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware;
use Slim\Factory\AppFactory;

// ...you'll need to set up your dependency injection, this example does
// not include that

$app = AppFactory::create();

$app->get('/some/route', SomeRoutable::class)->add(
    RequireOauthSessionLoginRedirectMiddleware::class,
);
```

```php
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

class SomeRoutable
{
    public function __invoke(ServerRequestInterface $request)
    {
        $userInfo = $request->getAttribute('oauthUserInfo');
        assert($userInfo instanceof OauthUserInfo);

        var_dump($userInfo->email);
    }
}
```
