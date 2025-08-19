# SendToLoginCreateAuthUrlEvent

If your application provides an instance of `\Psr\EventDispatcher\EventDispatcherInterface` through the PSR-11 container, then `\RxAnte\OAuth\SendToLoginResponseFactory::create` will dispatch an event, `\RxAnte\OAuth\SendToLoginCreateAuthUrlEvent` to allow your application to modify the URL. This is useful for adding query string parameters to communicate things to the auth server (for example, adding an `iss` param).

Here's an example using [Tukio](https://github.com/Crell/Tukio):

```php
use Crell\Tukio\OrderedProviderInterface;
use RxAnte\OAuth\SendToLoginCreateAuthUrlEvent;

readonly class SentToLoginCreateAuthUrlSubscriber
{
    public static function register(OrderedProviderInterface $provider): void
    {
        $provider->addSubscriber(self::class);
    }

    public function onApplyRoutes(SendToLoginCreateAuthUrlEvent $event): void
    {
        $iss = $event->findCookieValue('iss');

        if ($iss === null) {
            return;
        }

        $event->setUrl($event->url()->withQueryParam('iss', $iss));
    }
}
```
