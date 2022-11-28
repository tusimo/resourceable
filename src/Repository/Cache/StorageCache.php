<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository\Cache;

use Hyperf\Utils\Arr;
use Tusimo\Resource\Entity\CacheValue;
use Hyperf\Cache\Driver\DriverInterface;
use Tusimo\Resource\Contract\ResourceCacheAble;

class StorageCache extends AbstractCache implements ResourceCacheAble
{
    protected DriverInterface $cache;

    public function __construct(DriverInterface $cache, string $resourceName, string $keyName = 'id', string $prefix = 's')
    {
        $this->cache = $cache;
        parent::__construct($resourceName, $keyName, $prefix);
    }

    /**
     * Get resource by id.
     *
     * @param int|string $id
     */
    public function getResourceCache($id, array $select = []): CacheValue
    {
        [$result, $value] = $this->getCache()->fetch($this->getCacheKey($id), null);
        if (! $result) {
            return new CacheValue(null, false, $this->getKeyName());
        }
        if ($this->shouldSelect($select)) {
            $result = Arr::only($result, array_merge([$this->getKeyName()], $select));
        }
        return new CacheValue($value, true, $this->getKeyName());
    }

    /**
     * Get Resources by ids.
     */
    public function getResourcesCache(array $ids, array $select = []): array
    {
        $results = $this->getCache()->getMultiple($this->getCacheKeys($ids), 'missing');
        $resources = [];
        foreach ($results as $key => $value) {
            $key = $this->getOriginalKey($key);
            if ($value === 'missing') {
                $resources[$key] = new CacheValue(null, false, $this->getKeyName());
            } else {
                if ($this->shouldSelect($select)) {
                    $value = Arr::only($value, array_merge([$this->getKeyName()], $select));
                }
                $resources[$key] = new CacheValue($value, true, $this->getKeyName());
            }
        }
        return $resources;
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
        return $this->getCache()->delete($this->getCacheKey($id));
    }

    /**
     * Delete cache by resource ids.
     *
     * @return mixed
     */
    public function deleteResourcesCache(array $ids)
    {
        return $this->getCache()->deleteMultiple($this->getCacheKeys($ids));
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
        $this->getCache()->set($this->getCacheKey($id), $resource, $ttl);
    }

    /**
     * Set resources cache.
     *
     * @return mixed
     */
    public function setResourcesCache(array $resources, int $ttl)
    {
        $cachedResources = [];
        foreach ($resources as $resource) {
            if (empty($resource)) {
                continue;
            }
            $cachedResources[$this->getCacheKey($resource[$this->getKeyName()])] = $resource;
        }
        $this->getCache()->setMultiple($cachedResources, $ttl);
    }

    /**
     * Get the value of cache.
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the value of cache.
     *
     * @param mixed $cache
     * @return self
     */
    public function setCache($cache)
    {
        $this->cache = $cache;

        return $this;
    }
}
