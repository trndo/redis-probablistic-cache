<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Redis;

readonly class CacheService
{
    public function __construct(
        private Redis $redisClient,
        private LoggerInterface $logger
    ) {}

    public function getDefaultCachedValue(string $key, int $ttl = 60): int
    {
        $value = $this->redisClient->get($key);

        if (!$value) {
            $this->logger->info('DEFAULT_CACHE');
            $value = $this->getValue();
            $this->redisClient->setex($key, $ttl, $this->getValue());
        }

        return (int) $value;
    }

    public function getProbabilisticCached(string $key, int $ttl = 60, float $beta = 1): int
    {
        $cachedValue = (array) $this->redisClient->hGetAll($key);
        $value = $cachedValue['value'] ?? null;
        $expiration = $cachedValue['expiration'] ?? null;
        $delta = $cachedValue['delta'] ?? null;

        if (!$value || $this->recompute($delta, $beta, $expiration)) {
            if (!$value) {
                $this->logger->info('Value is not found!');
            }

            $this->logger->info('PROBABILISTIC_CACHE');
            $start = time();
            $value = $this->getValue();
            $delta = time() - $start;

            $this->redisClient->hMSet(
                $key,
                [
                    'expiration' => time() + $ttl,
                    'delta' => $delta,
                    'value' => $value,
                ]
            );
        }

        return (int)$value;
    }

    public function getValue(): int
    {
        $this->logger->info('Long computation simulation');
        //need to perform long computation
        sleep(4);

        return random_int(1, 40);
    }

    public function recompute(int $delta, float $beta, int $expiry): bool
    {
        $randomValue = mt_rand() / mt_getrandmax();

        return (time() - $delta * $beta * log($randomValue)) >= $expiry;
    }
}