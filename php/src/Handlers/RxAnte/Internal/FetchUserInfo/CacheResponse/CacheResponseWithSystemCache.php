<?php

declare(strict_types=1);

namespace RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\CacheResponse;

use DateTimeImmutable;
use Lcobucci\JWT\UnencryptedToken as JwtToken;
use Psr\Cache\CacheItemPoolInterface;
use RxAnte\OAuth\Handlers\RxAnte\Internal\FetchUserInfo\GetResponse\RxAnteResponse;

use function assert;

readonly class CacheResponseWithSystemCache implements CacheResponse
{
    public function __construct(private CacheItemPoolInterface $cachePool)
    {
    }

    public function cache(JwtToken $jwt, RxAnteResponse $response): void
    {
        $expirationDate = $jwt->claims()->get('exp');

        assert(
            $expirationDate === null ||
            $expirationDate instanceof DateTimeImmutable,
        );

        if ($expirationDate === null) {
            return;
        }

        $this->cachePool->save(
            $this->cachePool->getItem(CacheKey::get($jwt))
                ->set($response)
                // We only want to cache the response for as long as it's valid
                ->expiresAt($expirationDate),
        );
    }
}
