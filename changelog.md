# RxAnte OAuth Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.7.0 — 2026-11-10
### Security
- PHP: Updated `firebase/php-jwt` to `^7.2.1`. Versions before 7.0 have a security advisory (no minimum key size) and Composer now blocks them by default, which stopped apps from updating their dependencies.
- JS: Raised the minimum `next` version to `15.5.28` and `next-auth` to `4.24.15`. Earlier versions have critical security advisories. Next 14 is no longer supported and so has no updates for the afore-mentioned security advisories.
### Added
- JS: Support for Next 16 (`^16.4.1`). Apps on Next 16 previously got a second copy of Next installed inside this package.
- JS: `ioredis` is declared as an optional peer dependency. The IoRedis token repository and refresh lock already needed it; nothing new is installed.
### Changed
- PHP: `Auth0Config` and `FusionAuthConfig` now type `$signingCertificate` as `string|OpenSSLAsymmetricKey|OpenSSLCertificate`. Anything else, including a `resource`, now throws a `TypeError` when the config is created, instead of every token check failing.
- PHP: With php-jwt 7, the deprecated Auth0 and FusionAuth handlers reject tokens when the signing key is smaller than the algorithm's minimum. Standard 2048-bit RS256 keys are unaffected.

## 2.6.0 — 2026-07-14
### Added
- Added Pages Router / `getServerSideProps` request support: `RequestForSessionIdFactory`, `RefreshAccessTokenForSessionIdFactory`, `SessionIdFromPagesRequest`, and `OAUTH_SESSION_ID_COOKIE`. Lets Pages Router apps make authenticated server-side requests without `next/headers`. [Documentation](./documentation/javascript/request-for-session-id-factory.md).

## 2.5.0 — 2026-07-10
### Added
- Added support for supplying custom request headers to the JS `RequestFactory` request methods (`makeWithoutToken`, `makeWithToken`, `makeWithSignInRedirect`) via an optional `headers` property. Package managed headers (`Authorization`, `RequestType`, `Accept`, `Content-Type`, `Provider`) always take precedence and cannot be overridden. [Documentation](./documentation/javascript/request-factory.md).

## 2.4.0 — 2026-03-05
### Added
- Added `RequireM2MOauthTokenHeaderMiddleware`. [Documentation](./documentation/php/require-m2m-oauth-token-header-middleware.md).
### Fixed
- Fixed a bug/typo in the RxAnte Handler `RedisUserInfoFetchLock` that caused the lock to never be released.

## 2.3.0 — 2026-02-05
### Added
- Added a user info fetch lock to prevent multiple concurrent requests for the same user info. See [UserInfoFetchLock binding example in the Resource Server Usage documentation](./documentation/resource-server-usage.md)

## 2.2.0 — 2025-09-18
### Added
- Added `AuthCodeGrantApi.deleteSessionAndCookie()` method
- Added `TokenRepository.deleteTokenBySessionId()` method
- Added `TookenRepository.deleteTokenFromCookies()` method

## 2.1.0 — 2025-09-17
### Added
- Added a callback to `onBeforeSuccessRedirect` to `AuthCodeGrantApi.respondToAuthCodeCallback()`

## 2.0.0 — 2025-09-11
### Deprecated
- The use of the `next-auth` package has been deprecated (next-auth is bad and it should feel bad).
- The entirety of the PHP classes and functionality in the `\RxAnte\OAuth\Handlers\Auth0` and `\RxAnte\OAuth\Handlers\FusionAuth` namespace have been deprecated. Use the new generic handler classes in the `\RxAnte\OAuth\Handlers\RxAnte` namespace
- The class `\RxAnte\OAuth\Handlers\Common\ExtractToken` has been deprecated as it was only used in the old handler scenario
- The following exports in JS have been deprecated: NextAuthOptionsConfigFactory, NextAuthAuth0ProviderFactory, NextAuthFusionAuthProviderFactory, RefreshAccessTokenWithAuth0Factory. Use the new `AuthCodeGrantApi` for signing in (see documentation)
### Added
- Added a new generic PHP auth handler setup in the namespace: `\RxAnte\OAuth\Handlers\RxAnte`
- Added new JS `AuthCodeGrantApi` for handling OAuth sign-in without any `next-auth` involvement
- Added a request API for PHP apps to be make authenticated requests with Guzzle

## 1.6.0 — 2025-05-08
### Fixed
- Fixed an issue with Next15 `headers()` needing to be `await`ed.
### Added
- Added user data to the cookie and accounted for the cookie possibly being split up into multiple

## 1.5.0 — 2025-05-06
### Removed
- Removed the SignInPage export since it is not used and doesn't work and causes some problems
### Fixed
- Fixed an issue with Next15 `cookies()` needing to be `await`ed.
### Added
- Added the ability for making API requests to handle `conent-disposition` other than `inline`

## 1.4.1 - 2024-12-16
### Added
- Added support to OauthUserInfo for roles

## 1.4.0 - 2024-12-14
### Added
- Added support for [FusionAuth](https://fusionauth.io)

## 1.3.0 - 2024-12-06
### Added
- Released NPM package

## 1.2.0 - 2024-11-26
### Added
- Added allowed M2M subjects and signing certificate validation to make sure a security issue is not introduced by allowed invalid access tokens that have the right M2M ID

## 1.1.0 - 2024-11-25
### Added
- Added `RequestAccessToken` class for M2M access token requests

## 1.0.0 - 2024-11-14
### Added
- Initial release
