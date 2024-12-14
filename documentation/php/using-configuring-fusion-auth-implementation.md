# Using and Configuring the FusionAuth Implementation

In order to use the FusionAuth Implementation, you'll need to wire up and configure some things.

## CacheItemPoolInterface

You'll need to have an implementation of `Psr\Cache\CacheItemPoolInterface` available through your [PSR-11](https://www.php-fig.org/psr/psr-11/) container. Redis and the Symfony Redis implementation (`Symfony\Component\Cache\Adapter\RedisAdapter`) is recommended.

[PHP-DI](https://php-di.org) example:

```php
use DI\ContainerBuilder;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Redis;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use function DI\autowire;
use function DI\get as resolveFromContainer;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        CacheItemPoolInterface::class => resolveFromContainer(RedisAdapter::class),
        RedisAdapter::class => static function (ContainerInterface $di): RedisAdapter {
            $redis = $di->get(Redis::class);
            assert($redis instanceof Redis);

            return new RedisAdapter($redis);
        },
        Redis::class => static function (): Redis {
            $redis = new Redis();
            
            $redis->connect('host and other connection information here');
            
            return $redis;
        },
    ])
    ->build();
```

## RefreshLock

You'll need to have an implementation of `RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock` available through your [PSR-11](https://www.php-fig.org/psr/psr-11/) container. The implementation provided with this package relies on PHP's `\Redis`. See [Redis Refresh Lock Documentation](redis-refresh-lock.md)

## FusionAuthConfig

`RxAnte\OAuth\Handlers\FusionAuth\FusionAuthConfig` must be wired up with the appropriate values through your [PSR-11](https://www.php-fig.org/psr/psr-11/) container.

[PHP-DI](https://php-di.org) example:

```php
use DI\ContainerBuilder;
use RxAnte\OAuth\Handlers\FusionAuth\FusionAuthConfig;
use function DI\autowire;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        FusionAuthConfig::class => static function (): FusionAuthConfig {
            return new FusionAuthConfig(
                wellKnownUrl: 'https://some-sub-domain.us.auth0.com/.well-known/openid-configuration',
                // Load up your IdP's signing cert here. This is used to ensure
                // the validity of access tokens
                signingCertificate: getSigningCert(),
                sslVerify: true, // May need to be false in local dev with self-signed certificates
                // Optional items //
                signingCertificateAlgorithm: 'RS256',
                wellKnownCacheKey: 'some-custom-cache-key',
                wellKnownCacheExpiresAfter: new DateInterval('PT12H'), // default is PT24H
                m2mAuthorizedSubjects: ['SUB1', 'SUB2', 'ETC.'],
            );
        }
    ])
    ->build();
```
