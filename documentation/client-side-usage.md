# Client Side Usage

## NextJS

At RxAnte we use the [Next framework](https://nextjs.org/) for our web applications. Therefore, the Javascript component of this package is geared entirely for Next.

When making an authenticated request to a Resource Server, a `Bearer` token needs to be sent with the request. In order to do that, a token needs to be acquired.

### Sign In Oauth Request Cycle

In order to complete the `oauth` request cycle you'll need to create two endpoints in your Next app and use the `AuthCodeGrantApi` to respond to those requests. In order to use the `AuthCodeGrantApi`, you can use the `WellKnownAuthCodeGrantApiFactory` (which is recommended over the `AuthCodeGrantApiFactory`, though if your oauth server does not have or support a well-known openid config endpoint, you can use the `AuthCodeGrantApiFactory` and enter the endpoints manually).

#### `TokenRepositoryFactory`

You will first need an implementation of the `TokenRepository`. You can provide your own, but this package provides one for ioredis. To use that, create a factory function that looks about like this:

```typescript
import { TokenRepositoryForIoRedisFactory } from 'rxante-oauth';
import getRedisClient from './RedisClient';

export function TokenRepositoryFactory () {
    return TokenRepositoryForIoRedisFactory({
        /**
         * redis should be an instance of Redis from ioredis for storing tokens
         */
        redis: getRedisClient(),
        /**
         * redisTokenExpireTimeInSeconds should be set to the life expectancy
         * of refresh tokens. In this example 4800 seconds is 80 minutes
         */
        redisTokenExpireTimeInSeconds: 4800,
    });
}
```

#### `WellKnownAuthCodeGrantApiFactory`

Now for the `AuthCodeGrantApi`, create a factory function that looks something like this:

```typescript
import { WellKnownAuthCodeGrantApiFactory } from 'rxante-oauth';
import { TokenRepositoryFactory } from './TokenRepositoryFactory';
import getRedisClient from './RedisClient';

export async function AuthCodeGrantApiFactory () {
    return WellKnownAuthCodeGrantApiFactory({
        /**
         * Use the `TokenRepositoryFactory` from the example above
         */
        tokenRepository: TokenRepositoryFactory(),
        /**
         * Your Next application's URL
         */
        appUrl: 'https://APP_URL_HERE.tld',
        /**
         * The URL where the json for your auth server's openid-configuration
         * can be found
         */
        wellKnownUrl: 'https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration',
        /**
         * The configured client ID for your auth server's configuration for
         * this application
         */
        clientId: 'CLIENT_ID_HERE',
        /**
         * The configured client secret for your auth server's configuration
         * for this application
         */
        clientSecret: 'CLIENT_SECRET_HERE',
        /**
         * The URI in your Next application that will respond to the auth
         * server's callback
         */
        callbackUri: '/auth/callback',
        /**
         * Optional. This is an instance of Redis from ioredis for
         * caching the request for well-known information
         */
        redis: getRedisClient(),
        /**
         * Optional. Add the audience param if required
         * (needed for Auth0 primarily)
         */
        audience: 'SOME_AUDIENCE',
    });
}
```

#### Sign-in Routes

Now we're ready to create the `sign-in` and `callback` routes. These route URIs can be anything you want, but for the purposes of this demo, we'll do `/auth/sign-in` and `/auth/callback`.

#### `/auth/sign-in/route.ts`

At `/auth/sign-in` create a `route.ts` file that looks as follows:

```typescript
import { AuthCodeGrantApiFactory } from '../../AuthCodeGrantApiFactory';

export async function GET (request: Request) {
    return (await AuthCodeGrantApiFactory()).createSignInRouteResponse(request);
}
```

#### `/auth/callback/route.ts`

And at `/auth/callback` create a `route.ts` file that looks as follows:

```typescript
import { AuthCodeGrantApiFactory } from '../../AuthCodeGrantApiFactory';

export async function GET (request: Request) {
    return (await AuthCodeGrantApiFactory()).respondToAuthCodeCallback(request);
}
```

### Making Requests

Now that we are able to sign in and acquire a token, we can make requests. To make requests, you can use the `RequestFactory` to create the Request api.

#### `RequestFactory`

Create a factory method something like this:

```typescript
import {
    RequestFactory as BaseRequestFactory,
    RefreshAccessTokenFactory,
    IoRedisRefreshLockFactory,
} from 'rxante-oauth';
import { TokenRepositoryFactory } from './TokenRepositoryFactory';
import getRedisClient from './RedisClient';

export function RequestFactory () {
    const tokenRepository = TokenRepositoryFactory();

    return BaseRequestFactory({
        /**
         * Your Next application's URL
         */
        appUrl: 'https://APP_URL_HERE.tld',
        /**
         * The TLD URL to make API requests to (your resource server)
         */
        requestBaseUrl: 'https://RESOURCE_SERVER_URL_HERE.tld',
        tokenRepository,
        refreshAccessToken: RefreshAccessTokenFactory({
            tokenRepository,
            refreshLock: IoRedisRefreshLockFactory({ redis: getRedisClient() }),
            /**
             * The URL where the json for your auth server's openid-configuration
             * can be found
             */
            wellKnownUrl: 'https://AUTH_SERVER_URL_HERE/.well-known/openid-configuration',
            clientId: 'CLIENT_ID_HERE',
            clientSecret: 'CLIENT_SECRET_HERE',
            redis: getRedisClient(),
        }),
        signInUri: '/auth/sign-in',
    });
}
```

Now you can use the factory to create the Request api and make requests:

```tsx
import React from 'react';
import { RequestFactory } from './RequestFactory';

export default async function Page () {
    /**
     * If no token, or the token is invalid, the User will be redirected through
     * the oauth sign in flow and come back here after a token is acquired
     */
    console.log(
        await RequestFactory().makeWithSignInRedirect({
            uri: '/SOME_URI',
        })
    );

    /**
     * Or, you can just make the request with the token if it exists and get
     * the response back (which may be an unauthenticated response)
     */
    console.log(
        await RequestFactory().makeWithToken({
            uri: '/SOME_URI',
        })
    );

    /**
     * Or, if the request doesn't require a token, you can make an
     * unauthenticated request
     */
    console.log(
        await RequestFactory().makeWithoutToken({
            uri: '/SOME_URI',
        })
    );

    return <>Hello world!</>;
}
```

## PHP

At RxAnte, we use [Slim](https://www.slimframework.com/) to back our HTTP PHP applications with the [PSR-11](https://www.php-fig.org/psr/psr-11/) compliant dependency injection container, [PHP-DI]https://php-di.org/). All examples assume Slim with a PSR-11 compliant DI container.

While the vast majority of our Oauth needs are in Next, there may also be times when we need a PHP application to be authenticated via Oauth. For routes that need to be authenticated via Oauth, you can use the `\RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware` or the `\RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware` with your route.

### Dependencies

In order to use either of those middlwares, you'll need to wire up some dependencies. The following example demonstrates the needed dependencies:

```php
use DI\Container;
use Lcobucci\Clock\SystemClock;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Clock\ClockInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;
use RxAnte\OAuth\Handlers\Common\OauthPublicKey;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteConfig;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository;
use RxAnte\OAuth\Handlers\RxAnte\TokenRefresh\RxAnteGetRefreshedAccessToken;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactory;
use RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactoryConfig;
use RxAnte\OAuth\TokenRepository\Refresh\GetRefreshedAccessToken;
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RedisRefreshLock;
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock;
use RxAnte\OAuth\TokenRepository\TokenRepositoryConfig;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;
use Slim\Factory\AppFactory;
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

$container->set(
    UuidFactoryInterface::class,
    get(UuidFactory::class),
);

/**
 * While you can always implement your own `OauthUserInfoRepositoryInterface`
 * the one provided is designed to do the job
 */
$container->set(
    OauthUserInfoRepositoryInterface::class,
    get(RxAnteUserInfoRepository::class),
);

/**
 * Various classes will need an implementation of
 * `\League\OAuth2\Client\Provider\AbstractProvider`. The
 * `\RxAnte\OAuth\Handlers\RxAnte\WellKnownProviderFactory` is designed to
 * create one from a well-known oauth config endpoint.
 */
$container->set(
    AbstractProvider::class,
    static function (ContainerInterface $di): AbstractProvider {
        return $di->get(WellKnownProviderFactory::class)->create();
    },
);

$container->set(
    TokenRepositoryConfig::class,
    static function (): TokenRepositoryConfig {
        return new TokenRepositoryConfig(
        /**
         * redisTokenExpireTimeInSeconds should be set to the life expectancy
         * of refresh tokens. In this example 4800 seconds is 80 minutes
         */
            expireInSeconds: 4800,
        );
    },
);

$container->set(
    RefreshLock::class,
    get(RedisRefreshLock::class),
);

$container->set(
    GetRefreshedAccessToken::class,
    get(RxAnteGetRefreshedAccessToken::class),
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
    WellKnownProviderFactoryConfig::class,
    static function (): WellKnownProviderFactoryConfig {
        return new WellKnownProviderFactoryConfig(
            appBaseUrl: 'https://PHP_APP_URL.tld',
            clientId: 'CLIENT_ID_HERE',
            clientSecret: 'CLIENT_SECRET_HERE',
            audience: 'SOME_AUDIENCE', // optional
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

### `\RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware`

This middleware will display an access denied message if the user is not logged in via Oauth. You are responsible for getting the user logged in with this middleware.

```php
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware;
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

    $response->getBody()->write(
        'Hello world. I am logged in as ' . $userInfo->name,
    );

    return $response;
})->add(RequireOauthSessionAccessDeniedMiddleware::class);

$app->run();
```

### `\RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware`

With the redirect middleware, if the user is not logged in, they will be redirected through the Oauth token request process if they do not have a token, or the token is invalid.

```php
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware;
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

    $response->getBody()->write(
        'Hello world. I am logged in as ' . $userInfo->name,
    );

    return $response;
})->add(RequireOauthSessionLoginRedirectMiddleware::class);

$app->run();
```

### Making an authenticated request in a PHP client

Assuming you have protected your route with one of the middlewares above, you have a token stored, and now you need to make a request with the token. Use the RequestApi (`\RxAnte\OAuth\Request\RequestApi`) to do so easily:

```php
use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Request\Payload;
use RxAnte\OAuth\Request\QueryParams;
use RxAnte\OAuth\Request\RequestApi;
use RxAnte\OAuth\Request\RequestMethod;
use RxAnte\OAuth\Request\RequestProperties;
use RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

/** wire dependencies */

$app = AppFactory::create(container: $container);

$app->get('/', function (
    ServerRequestInterface $request,
    ResponseInterface $response,
) use ($container): ResponseInterface {
    $requestApi = $container->get(RequestApi::class);

    $apiResponse = $requestApi->makeWithTokenFromRequestCookies(
        serverRequest: $request,
        // Optional, but you'll probably always want to set a URI at least
        properties: new RequestProperties(
            /**
             * If you always make API requests to the same base URL, you
             * can set up your DI to provide \RxAnte\OAuth\Request\RequestApiConfig
             * with the base URL and then here you only need `/some/endpoint`.
             * Even with a base URL defined, if you put a full URL here, the
             * full URL will be used instead of the base URL
             */
            uri: 'https://myapi.com/some/endpoint',
            // Optional, defaults to RequestMethod:GET
            method: RequestMethod::POST,
            // Optional: supply some query params
            queryParams: new QueryParams([
                'foo' => 'bar',
                'bar' => 'baz',
            ]),
            // Optional: You may wish to send some data to the api with request
            payload: new Payload([
                'emailAddress' => 'foo@bar.baz',
                'message' => 'Lorem ipsum',
            ]),
        ),
    );

    $response->getBody()->write(
        'Hello world: ' . $apiResponse->getJson()['message'],
    );

    return $response;
})->add(RequireOauthSessionLoginRedirectMiddleware::class);

$app->run();
```
