## Redis Refresh Lock

You'll need to have an implementation of `RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock` available through your [PSR-11](https://www.php-fig.org/psr/psr-11/) container. The implementation provided with this package relies on PHP's `\Redis`.

[PHP-DI](https://php-di.org) example:

See example above for configuring `Redis` in PHP-DI. Additionally add this to the `addDefinitions` array to use the provided `Redis` implementation:

```php
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RedisRefreshLock;
use RxAnte\OAuth\TokenRepository\Refresh\Lock\RefreshLock;
use function DI\get as resolveFromContainer;

[
    RefreshLock::class => resolveFromContainer(RedisRefreshLock::class),
]
```
