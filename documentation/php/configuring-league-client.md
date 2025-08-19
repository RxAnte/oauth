# Configuring the League Client

When using `\RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware` or `\RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware`, the League client will need to be configured. The recommended way is to use the factory: `\RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactory`.

## DI config for `\RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactory`

```php
use DI\ContainerBuilder;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Container\ContainerInterface;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactory;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactoryConfig;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        RxAnteConfig::class => static fn () => new RxAnteConfig(
            wellKnownUrl: 'https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration',
        ),
        WellKnownProviderFactoryConfig::class => static fn () => new WellKnownProviderFactoryConfig(
            appBaseUrl: 'https://APP_URL_HERE.tld',
            clientId: 'CLIENT_ID_HERE',
            clientSecret: 'CLIENT_SECRET_HERE',
        ),
        AbstractProvider::class => static fn (ContainerInterface $di) => $di->get(
            WellKnownProviderFactory::class,
        )->create(),
    ])
    ->build();
```

## Routes

You'll also need to add the routes needed for the token callbacks. You can get the routes from the `create` method on `\RxAnte\OAuth\Routes\RoutesFactory`.

## [Slim 4](https://www.slimframework.com) example

```php
use DI\ContainerBuilder;
use RxAnte\OAuth\Routes\Route;
use RxAnte\OAuth\Routes\RoutesFactory;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        // ...definitions
    ])
    ->build();

AppFactory::setContainer($di);
$app = AppFactory::create();

$oauthRoutesFactory = $di->get(RoutesFactory::class);
$oauthRoutes = $oauthRoutesFactory->create();
$oauthRoutes->map(
    static function (Route $route) use ($app): void {
        $app->map(
            [$route->requestMethod->name],
            $route->pattern,
            $route->class,
        );
    },
);

$app->run();
```
