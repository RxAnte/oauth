<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo;

use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Psr\Container\ContainerInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheKey;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse\CacheResponseFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\GetRxAnteResponseFactory;
use RxAnte\OAuth\UserInfo\OauthUserInfo;
use RxAnte\OAuth\UserInfo\RateLimit;

use function is_array;
use function json_decode;

readonly class FetchUserInfoFromRxAnteAuth implements FetchUserInfo
{
    private UserInfoFetchLock $fetchLock;

    public function __construct(
        ContainerInterface $di,
        private CacheResponseFactory $cacheResponseFactory,
        private GetRxAnteResponseFactory $getResponseFactory,
    ) {
        if ($di->has(UserInfoFetchLock::class)) {
            $this->fetchLock = $di->get(UserInfoFetchLock::class);

            return;
        }

        $this->fetchLock = $di->get(UserInfoFetchNoOp::class);
    }

    /** @throws RateLimit */
    public function fetch(JwtToken $jwt): OauthUserInfo
    {
        $responseWrapper = $this->getResponseFactory
            ->create($jwt)
            ->get($jwt);

        $this->cacheResponseFactory->create($responseWrapper)
            ->cache($jwt, $responseWrapper->response);

        // Always make sure the lock is released here in case it got set
        $this->fetchLock->release(CacheKey::get($jwt));

        if ($responseWrapper->response->statusCode === 429) {
            throw new RateLimit();
        }

        if ($responseWrapper->response->isNotValid()) {
            return new OauthUserInfo();
        }

        $userInfo = json_decode(
            $responseWrapper->response->body,
            true,
        );

        /**
         * Defensive coding. If we don't have valid json, json_decode will not
         * return an array, and we'll get exceptions below
         */
        $userInfo = is_array($userInfo) ? $userInfo : [];

        $sub = (string) ($userInfo['sub'] ?? '');

        $roles = $userInfo['roles'] ?? [];

        if (! is_array($roles)) {
            $roles = [];
        }

        return new OauthUserInfo(
            $sub !== '',
            $sub,
            $userInfo['email'] ?? '',
            $userInfo['name'] ?? '',
            $userInfo['given_name'] ?? '',
            $userInfo['family_name'] ?? '',
            $userInfo['picture'] ?? '',
            $roles,
        );
    }
}
