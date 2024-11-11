# RequireValidOauthSessionUserMiddleware

`\RxAnte\OAuth\RequireValidOauthSessionUserMiddleware`

A [PSR-15](https://github.com/php-fig/http-server-middleware) server middleware implementation.

This middleware can be used to require a valid token to be present in a user's session. If the token stored in the session is valid, a `\RxAnte\OAuth\UserInfo\OauthUserInfo` instance will be added to the request attributes with the name `oauthUserInfo`. If there is no valid session, the user will be sent through the OAuth process to acquire a token to store on the session.

This is great for use with browser PHP application access.

> [!NOTE]
> `RequireValidOauthSessionUserMiddleware` requires an implementation of [OauthUserInfoRepositoryInterface](oauth-user-info-repository-interface.md). This package provides an Auth0 implementation.

> [!NOTE]
> `RequireValidOauthSessionUserMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken`. This package provides an Auth0 implementation. See [Using and Configuring the Auth0 Implementation](using-configuring-auth0-implementation.md)

> [!NOTE]
> `RequireValidOauthSessionUserMiddleware` requires an implementation of `RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock`. This package provides a Redis implementation. See [Using and Configuring the Auth0 Implementation](using-configuring-auth0-implementation.md)

> [!NOTE]
> `RequireValidOauthSessionUserMiddleware` requires an implementation of `League\OAuth2\Client\Provider\AbstractProvider`. You can learn how to implement that configuration [here](configuring-league-client.md).

## [Slim 4](https://www.slimframework.com) example

To use this middleware, add it to any route that needs to be protected.

```php
use RxAnte\OAuth\RequireValidOauthSessionUserMiddleware;
use Slim\Factory\AppFactory;

// ...you'll need to set up your dependency injection, this example does
// not include that

$app = AppFactory::create();

$app->get('/some/route', SomeRoutable::class)->add(
    RequireValidOauthSessionUserMiddleware::class,
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
