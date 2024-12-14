# OauthUserInfoRepositoryInterface

An implementation of `\RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface` must be wired up in your [PSR-11](https://www.php-fig.org/psr/psr-11/) container for pretty much anything in this package to work.

This package supplies two at this time

1. [Auth0](https://auth0.com).
2. [FusionAuth](https://fusionauth.io)

You can always implement your own and provide it as well.

## Auth0 example with [PHP-DI](https://php-di.org)

```php
use DI\ContainerBuilder;
use RxAnte\OAuth\Handlers\Auth0\Auth0UserInfoRepository;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;

use function DI\get as resolveFromContainer;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        OauthUserInfoRepositoryInterface::class => resolveFromContainer(Auth0UserInfoRepository::class),
    ])
```

Also see [Using and Configuring the Auth0 Implementation](using-configuring-auth0-implementation.md)

## FusionAuth example with [PHP-DI](https://php-di.org)

```php
use DI\ContainerBuilder;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;
use \RxAnte\OAuth\Handlers\FusionAuth\FusionAuthUserInfoRepository;

use function DI\get as resolveFromContainer;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        OauthUserInfoRepositoryInterface::class => resolveFromContainer(FusionAuthUserInfoRepository::class),
    ])
```

Also see [Using and Configuring the Auth0 Implementation](using-configuring-fusion-auth-implementation.md)
