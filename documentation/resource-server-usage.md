# Resource Server Usage

RxAnte uses PHP for our resource servers. And we use [Slim](https://www.slimframework.com/) to back our HTTP PHP applications with the [PSR-11](https://www.php-fig.org/psr/psr-11/) compliant dependency injection container, [PHP-DI](https://php-di.org/). All examples assume Slim with a PSR-11 compliant DI container.

In order to protect resource routes and require a bearer token, you can use the `\RxAnte\OAuth\RequireOauthTokenHeaderMiddleware` with your route.

### Dependencies

In order to use the middleware, you'll need to wire up some dependencies. The following example demonstrates the needed dependencies:

```php
use DI\Container;
use Lcobucci\Clock\SystemClock;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use RxAnte\OAuth\Handlers\Common\OauthPublicKey;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Symfony\Component\Cache\Adapter\RedisAdapter;

use function DI\get;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

/**
 * Redis implementations of various things are used by RxAnte and assumed in
 * examples below. If you wish to use something else, you'll have to provide
 * your own implementations of various interfaces
 */

$container->set(
    Redis::class,
    static function (ContainerInterface $container): Redis {
        $redis = new Redis();
        $redis->connect('HOST_HERE');

        return $redis;
    },
);

$container->set(
    RedisAdapter::class,
    static function (ContainerInterface $container): RedisAdapter {
        return new RedisAdapter($container->get(Redis::class));
    },
);

$container->set(
    CacheItemPoolInterface::class,
    get(RedisAdapter::class),
);

$container->set(
    ClockInterface::class,
    static function (): ClockInterface {
        return SystemClock::fromUTC();
    },
);

/**
 * While you can always implement your own `OauthUserInfoRepositoryInterface`
 * the one provided is designed to do the job
 */
$container->set(
    OauthUserInfoRepositoryInterface::class,
    get(RxAnteUserInfoRepository::class),
);

$container->set(
    OauthPublicKey::class,
    static fn () => new OauthPublicKey(
        __DIR__ . '/PATH_TO_PUBLIC_SIGNING_KEY',
    ),
);

$container->set(
    RxAnteConfig::class,
    static function (ContainerInterface $container): RxAnteConfig {
        return new RxAnteConfig(
            'https://AUTH_SERER.tls/.well-known/openid-configuration',
        );
    },
);

$container->set(
    ResponseFactoryInterface::class,
    get(ResponseFactory::class),
);

$app = AppFactory::create(container: $container);

$app->run();
```

## `\RxAnte\OAuth\RequireOauthTokenHeaderMiddleware`

To secure a route and ensure a valid oauth bearer token is present in the request, use `\RxAnte\OAuth\RequireOauthTokenHeaderMiddleware`:

```php
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\RequireOauthTokenHeaderMiddleware;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

/** wire dependencies */

$app = AppFactory::create(container: $container);

$app->get('/', static function (
    ServerRequestInterface $request,
    ResponseInterface $response,
) {
    $userInfo = $request->getAttribute('oauthUserInfo');
    assert($userInfo instanceof OauthUserInfo);

    $response->getBody()->write((string) json_encode([
        'success' => true,
        'message' => 'You have loaded the resource successfully as ' . $userInfo->name,
    ]));

    return $response;
})->add(RequireOauthTokenHeaderMiddleware::class);

$app->run();
```
