<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository\Cache;

use Hyperf\Redis\Redis;
use Tusimo\Resource\Entity\CacheValue;
use Tusimo\Resource\Contract\ResourceCacheAble;

class RedisHashCache extends AbstractCache implements ResourceCacheAble
{
    protected Redis $redis;

    public function __construct(Redis $redis, string $resourceName, string $keyName = 'id', string $prefix = 'h')
    {
        $this->redis = $redis;
        $prefix = env('APP_NAME', 'php-template') . ':' . $prefix;
        parent::__construct($resourceName, $keyName, $prefix);
    }

    /**
     * Get resource by id.
     *
     * @param int|string $id
     */
    public function getResourceCache($id, array $select = []): CacheValue
    {
        if ($this->shouldSelect($select)) {
            $result = $this->getRedis()->hMGet($this->getCacheKey($id), $select);
        } else {
            $result = $this->getRedis()->hGetAll($this->getCacheKey($id));
        }
        if (! isset($result[$this->getKeyName()])) {
            return new CacheValue([], false, $this->getKeyName());
        }
        return new CacheValue($result, true, $this->getKeyName());
    }

    /**
     * Get Resources by ids.
     */
    public function getResourcesCache(array $ids, array $select = []): array
    {
        $ids = array_values($ids);
        if (empty($ids)) {
            return [];
        }
        $pipe = $this->getRedis()->multi();
        if ($this->shouldSelect($select)) {
            foreach ($ids as $id) {
                $pipe = $pipe->hMGet($this->getCacheKey($id), $select);
            }
        } else {
            foreach ($ids as $id) {
                $pipe = $pipe->hGetAll($this->getCacheKey($id));
            }
        }
        $results = [];
        $resources = $pipe->exec();
        foreach ($ids as $idx => $id) {
            if (! empty($resources[$idx]) && isset($resources[$idx][$this->getKeyName()])) {
                $results[$id] = new CacheValue($resources[$idx], true, $this->getKeyName());
            } else {
                $results[$id] = new CacheValue([], false, $this->getKeyName());
            }
        }
        return $results;
    }

    /**
     * Delete cache by resource id.
     *
     * @param int|string $id
     *
     * @return mixed
     */
    public function deleteResourceCache($id)
    {
        return $this->getRedis()->del($this->getCacheKey($id));
    }

    /**
     * Delete cache by resource ids.
     *
     * @return mixed
     */
    public function deleteResourcesCache(array $ids)
    {
        return $this->getRedis()->del(...$this->getCacheKeys($ids));
    }

    /**
     * Set resource cache.
     *
     * @param int|string $id
     * @param mixed $resource
     *
     * @return mixed
     */
    public function setResourceCache($id, $resource, int $ttl)
    {
        $pipe = $this->getRedis()->multi(\Redis::PIPELINE);
        $pipe->hMSet($this->getCacheKey($id), $resource);
        $pipe->expire($this->getCacheKey($id), $ttl);
        $pipe->exec();
    }

    /**
     * Set resources cache.
     *
     * @return mixed
     */
    public function setResourcesCache(array $resources, int $ttl)
    {
        $pipe = $this->getRedis()->multi(\Redis::PIPELINE);
        foreach ($resources as $resource) {
            $pipe->hMSet($this->getCacheKey($resource[$this->getKeyName()]), $resource);
            $pipe->expire($this->getCacheKey($resource[$this->getKeyName()]), $ttl);
        }
        return $pipe->exec();
    }

    public function getRedis(): Redis
    {
        return $this->redis;
    }

    public function setRedis(Redis $redis)
    {
        $this->redis = $redis;
    }
}
