# Custom Authentication Hook

In some cases, you may wish to do additional authentication against your database or some local user-data, or what have you. Both `RequireValidOauthSessionUserMiddleware` and `RequireValidOauthTokenHeaderMiddleware` check for an implementation of `\RxAnte\OAuth\CustomAuthenticationHook` in the `\RxAnte\OAuth\CustomAuthenticationHookFactory`. If you would like to provide custom authentication, create an implementation of `\RxAnte\OAuth\CustomAuthenticationHook` and add it to your container.

[PHP-DI](https://php-di.org) example

```php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\CustomAuthenticationHook;
use RxAnte\OAuth\CustomAuthenticationResult;
use RxAnte\OAuth\UserInfo\OauthUserInfo;

readonly class MyAppAuthHook implements CustomAuthenticationHook
{
    public function __construct(private UserRepository $userRepository)
    {
    }
    
    public function process(
        OauthUserInfo $userInfo,
        ServerRequestInterface $request,
        ResponseInterface $defaultAccessDeniedResponse,
    ): CustomAuthenticationResult {
        // As an example, perhaps you have a user repository to fetch local
        // data about the user
        $user = $this->userRepository->findUserByEmailAddress(
            $userInfo->email,
        );

        // Check against your own custom properties according to app needs
        if ($user === null || ! $user->isActive) {
            // Sending a response object will return the provided response in
            // the middleware, rather than passing to the handler for response
            return new CustomAuthenticationResult(
                // Use the default access denied response provided by this
                // package, or you can also build your own
                response: $defaultAccessDeniedResponse,
            );
        }
        
        return new CustomAuthenticationResult(
            // Optionally, providing a new request object will replace the
            // previous request object
            request: $request->withAttribute('user', $user),
        );
    }
}
```

```php
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use RxAnte\OAuth\CustomAuthenticationHookFactory;

use function DI\get as resolveFromContainer;

$di = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions([
        CustomAuthenticationHookFactory::class => static function (ContainerInterface $di) {
            return new CustomAuthenticationHookFactory(
                $di->get(MyAppAuthHook::class),
            );
        },
    ])
```
