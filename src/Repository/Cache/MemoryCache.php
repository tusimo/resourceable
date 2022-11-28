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
use Tusimo\Resource\Utils\MemoryCollection;
use Tusimo\Resource\Contract\ResourceCacheAble;
use Tusimo\Resource\Utils\MemoryCollectionManager;

class MemoryCache extends AbstractCache implements ResourceCacheAble
{
    protected MemoryCollection $cache;

    public function __construct(string $resourceName, string $keyName = 'id', string $prefix = '')
    {
        parent::__construct($resourceName, $keyName, $prefix);
        $this->cache = MemoryCollectionManager::getCollection($this->getResourceName());
    }

    /**
     * Get resource by id.
     *
     * @param int|string $id
     */
    public function getResourceCache($id, array $select = []): CacheValue
    {
        $id = $this->getCacheKey($id);
        $result = $this->cache->get($id, 'missing');
        if ($result === 'missing') {
            return new CacheValue(null, false, $this->getKeyName());
        }
        if ($this->shouldSelect($select)) {
            $result = Arr::only([$result], array_merge([$this->getKeyName()], $select));
        }
        return new CacheValue($result, true, $this->getKeyName());
    }

    /**
     * Get Resources by ids.
     */
    public function getResourcesCache(array $ids, array $select = []): array
    {
        $resources = [];
        foreach ($ids as $id) {
            $resources[$id] = $this->getResourceCache($id);
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
        $id = $this->getCacheKey($id);

        return $this->cache->forget($id);
    }

    /**
     * Delete cache by resource ids.
     *
     * @return mixed
     */
    public function deleteResourcesCache(array $ids)
    {
        $ids = $this->getCacheKeys($ids);
        return $this->cache->forget($ids);
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
        $id = $this->getCacheKey($id);

        $this->cache->put($id, $resource);
        $this->setCacheExpiring($id, $ttl);
    }

    /**
     * Set resources cache.
     *
     * @return mixed
     */
    public function setResourcesCache(array $resources, int $ttl)
    {
        foreach ($resources as $item) {
            $this->setResourceCache($item[$this->getKeyName()], $item, $ttl);
        }
    }
}
