# OauthUserInfoRepositoryInterface

An implementation of `\RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface` must be wired up in your [PSR-11](https://www.php-fig.org/psr/psr-11/) container for pretty much anything in this package to work.

The implementation supplied with this package at this time is for [Auth0](https://auth0.com). You can always implement your own and provide it as well.

## [PHP-DI](https://php-di.org) example

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
