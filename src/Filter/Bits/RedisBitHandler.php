<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Filter\Bits;

use Hyperf\Redis\Redis;

class RedisBitHandler implements BitHandlerContract
{
    protected Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * Set the hash bit value into the specified bucket.
     *
     * @param int $value should be 1 or 0
     */
    public function setBit(string $bucket, int $hash, int $value)
    {
        $this->redis->setBit($bucket, $hash, (bool) $value);
    }

    /**
     * Set the hash bits value into the specified bucket.
     */
    public function setBits(string $bucket, array $hashValues)
    {
        if (empty($hashValues)) {
            return;
        }
        $this->redis->multi();
        foreach ($hashValues as $hash => $value) {
            $this->redis->setBit($bucket, $hash, (bool) $value);
        }
        $this->redis->exec();
    }

    /**
     * Get the value in the specified bucket.
     */
    public function getBit(string $bucket, int $hash): int
    {
        return $this->redis->getBit($bucket, $hash);
    }

    /**
     * Get the value in the specified bucket.
     */
    public function getBits(string $bucket, array $hashArray): array
    {
        if (empty($hashArray)) {
            return [];
        }
        $this->redis->multi();
        foreach ($hashArray as $hash) {
            $this->redis->getBit($bucket, $hash);
        }
        $results = $this->redis->exec();
        $hashes = [];
        foreach ($hashArray as $idx => $hash) {
            $hashes[$hash] = $results[$idx];
        }
        return $hashes;
    }

    /**
     * Empty the bucket.
     */
    public function empty(string $bucket)
    {
        $this->redis->del($bucket);
    }

    /**
     * Check the bucket is empty or not.
     */
    public function isEmpty(string $bucket): bool
    {
        return $this->redis->exists($bucket) && $this->redis->bitCount($bucket) >= 1;
    }
}
