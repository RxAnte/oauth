# RequireM2MOauthTokenHeaderMiddleware

`\RxAnte\OAuth\RequireM2MOauthTokenHeaderMiddleware`

A [PSR-15](https://github.com/php-fig/http-server-middleware) server middleware implementation.

This middleware ensures that a valid Machine to Machine bearer token header is present in an HTTP request.

If there is no valid Machine to Machine bearer token in the request headers, an access denied response with HTTP 403 status code will be sent.

You can also implement an instance of `\RxAnte\OAuth\CustomAuthenticationHook` to do any custom authentication your application needs, and/or create a custom `\Psr\Http\Message\ResponseInterface` to respond with. See the [Custom Authentication Hook documentation](custom-auth-hook.md) for more details.

> [!NOTE]
> `RequireM2MOauthTokenHeaderMiddleware` requires an implementation of [OauthUserInfoRepositoryInterface](oauth-user-info-repository-interface.md). This package provides an implementation which will need to be configured: `\RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository`.

## [Slim 4](https://www.slimframework.com) example

To use this middleware, add it to any route that needs to be protected.

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\RequireM2MOauthTokenHeaderMiddleware;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use Slim\Factory\AppFactory;

// ...you'll need to set up your dependency injection, this example does
// not include that

$app = AppFactory::create();

$app->get('/some/route', static function (
    ServerRequestInterface $request,
    ResponseInterface $response,
): ResponseInterface {
    $response->getBody()->write((string) json_encode([
        'success' => true,
    ]));

    return $response;
})->add(RequireM2MOauthTokenHeaderMiddleware::class);
```
