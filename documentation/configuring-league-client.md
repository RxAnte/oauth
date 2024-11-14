# Configuring the League Client

When using `\RxAnte\OAuth\RequireValidOauthSessionUserMiddleware` the League client will need to be configured. If using the provided `Auth0` implementation, use and configure `\RxAnte\OAuth\Handlers\Auth0\Auth0LeagueOauthProvider`. Otherwise, use and configure whatever implementation of `League\OAuth2\Client\Provider\AbstractProvider` you need.

[PHP-DI](https://php-di.org) example:

```php
use DI\ContainerBuilder;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Container\ContainerInterface;
use RxAnte\OAuth\Handlers\Auth0\Auth0LeagueOauthProviderConfig;
use RxAnte\OAuth\Handlers\Auth0\Auth0LeagueOauthProviderFactory;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        Auth0LeagueOauthProviderConfig::class => static function (): Auth0LeagueOauthProviderConfig {
            return new Auth0LeagueOauthProviderConfig(
                clientId: 'REPLACE_WITH_CLIENT_ID',
                clientSecret: 'REPLACE_WITH_CLIENT_SECRET',
                callbackDomain: 'https://REPLACE_WITH_APP_DOMAIN.com',
                audience: 'some-audience-identifier',
                // Optional, default is example below
                scopes: [
                    'openid',
                    'profile',
                    'email',
                    'offline_access',
                ],
            );
        }
        AbstractProvider::class => static function (ContainerInterface $di) {
            return $di->get(Auth0LeagueOauthProviderFactory::class)->create();
        }
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
