# RxAnte OAuth Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 2.0.0 — 2025-08-18
### Deprecated
- The use of the `next-auth` package has been deprecated (next-auth is bad and it should feel bad).
- The entirety of the PHP classes and functionality in the `\RxAnte\OAuth\Handlers\Auth0` and `\RxAnte\OAuth\Handlers\FusionAuth` namespace have been deprecated. Use the new generic handler classes in the `\RxAnte\OAuth\Handlers\RxAnte` namespace
- The class `\RxAnte\OAuth\Handlers\Common\ExtractToken` has been deprecated as it was only used in the old handler scenario
- The following exports in JS have been deprecated: NextAuthOptionsConfigFactory, NextAuthAuth0ProviderFactory, NextAuthFusionAuthProviderFactory, RefreshAccessTokenWithAuth0Factory. Use the new `AuthCodeGrantApi` for signing in (see documentation)
### Added
- Added a new generic PHP auth handler setup in the namespace: `\RxAnte\OAuth\Handlers\RxAnte`
- Added new JS `AuthCodeGrantApi` for handling OAuth sign-in without any `next-auth` involvement

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
