# RequireOauthTokenHeaderMiddleware

`\RxAnte\OAuth\RequireOauthTokenHeaderMiddleware`

A [PSR-15](https://github.com/php-fig/http-server-middleware) server middleware implementation.

This middleware can be used to validate a bearer token header and add the resulting user info to the request object. If the token is valid, a `\RxAnte\OAuth\UserInfo\OauthUserInfo` instance will be added to the request attributes with the name `oauthUserInfo`.

Great for use with APIs that need to be authenticated.

> [!NOTE]
> `RequireOauthTokenHeaderMiddleware` requires an implementation of [OauthUserInfoRepositoryInterface](oauth-user-info-repository-interface.md). This package provides an Auth0 implementation and a FusionAuth implementation, either of which will need to be configured in order to use.

## [Slim 4](https://www.slimframework.com) example

To use this middleware, add it to any route that needs to be protected.

```php
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use Slim\Factory\AppFactory;

// ...you'll need to set up your dependency injection, this example does
// not include that

$app = AppFactory::create();

$app->get('/some/route', SomeRoutable::class)->add(
    RequireOauthTokenHeaderMiddleware::class,
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
