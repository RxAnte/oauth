# OauthUserInfoRepositoryInterface

An implementation of `\RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface` must be wired up in your [PSR-11](https://www.php-fig.org/psr/psr-11/) container for pretty much anything in this package to work.

This package has an implementation which will probably suite your needs: `\RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository`

You can also implement your own and provide it.

## Example with [PHP-DI](https://php-di.org)

```php
use DI\ContainerBuilder;
use RxAnte\OAuth\UserInfo\OauthUserInfoRepositoryInterface;
use RxAnte\OAuth\Handlers\RxAnte\RxAnteUserInfoRepository;

use function DI\get as resolveFromContainer;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        OauthUserInfoRepositoryInterface::class => resolveFromContainer(RxAnteUserInfoRepository::class),
    ])
```
