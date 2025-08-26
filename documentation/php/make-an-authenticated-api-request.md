# Make An Authenticated API Request

Assuming you have protected a route with `\RxAnte\OAuth\RequireOauthSessionLoginRedirectMiddleware` or `\RxAnte\OAuth\RequireOauthSessionAccessDeniedMiddleware`, you may wish to make an authenticated API request.

The RequestApi (`\RxAnte\OAuth\Request\RequestApi`) make it easy to do so.

You can get the RequestApi from your PSR-11 container, then use one of the following methods to make your API request.

## `\RxAnte\OAuth\Request\RequestApiConfig`

You can optionally wire up your DI container `\RxAnte\OAuth\Request\RequestApiConfig` to supply a `requestBaseUrl` if you always or often make API requests to the same URL.

## `makeWithToken` Method

You probably won't need to use this often, but it is here if you have the AccessTokenInterface (`\League\OAuth2\Client\Token\AccessTokenInterface`) you wish to use.

### `makeWithToken` argument 1: `\League\OAuth2\Client\Token\AccessTokenInterface`

You can acquire the AccessTokenInterface from it's storage in the TokenRepository.

### `makeWithToken` argument 2: `\RxAnte\OAuth\Request\RequestProperties`

While an optional argument, you will almost always need to supply it to provide at least a URI. See the end of this document for more on RequestProperties.

## `makeWithTokenFromRequestCookies` Method

If you have the `\Psr\Http\Message\ServerRequestInterface` object (such as when initiating from a Slim route), use this method and supply the request.

### `makeWithTokenFromRequestCookies` argument 1: `\Psr\Http\Message\ServerRequestInterface`

The request from your route.

### `makeWithTokenFromRequestCookies` argument 2: `\RxAnte\OAuth\Request\RequestProperties`

While an optional argument, you will almost always need to supply it to provide at least a URI. See the end of this document for more on RequestProperties.

## `makeWithTokenFromCookieGlobals` Method

Use this method if you don't have access to the Request object. The session ID will be retrieved from PHP's global `$_COOKIE` store.

### `makeWithTokenFromCookieGlobals` argument 1: `\RxAnte\OAuth\Request\RequestProperties`

While an optional argument, you will almost always need to supply it to provide at least a URI. See the end of this document for more on RequestProperties.

## `makeWithoutToken` Method

For the sake of completeness, sometimes you need to make an unauthenticated request. This method returns the same `RequestResponse` object, and sets the same `Accept` and `Content-Type` headers as the other methods so you can use this RequestApi for all your requests, including un-authenticated.

### `makeWithoutToken` argument 1: `\RxAnte\OAuth\Request\RequestProperties`

While an optional argument, you will almost always need to supply it to provide at least a URI. See the end of this document for more on RequestProperties.

## `\RxAnte\OAuth\Request\RequestProperties`

Any request method you use will need one of these Classes sent in. The constructor accepts the following arguments:

### `uri`

The uri you wish to load. You can supply a full URL `https://myresourceserver.com/some/endpoint`, or if you have wired up `\RxAnte\OAuth\Request\RequestApiConfig` with a base URL, you can supply a path. Even when you've wired up RequestApiConfig, if you supply a full URL here, the full URL will be used.

### `method` (optional)

The method accepts an enum of `\RxAnte\OAuth\Request\RequestMethod`.

### `queryParams` (optional)

The queryParams accepts a class of `\RxAnte\OAuth\Request\QueryParams`.

### `Payload` (optional)

The payload accepts a class of `\RxAnte\OAuth\Request\Payload`.
