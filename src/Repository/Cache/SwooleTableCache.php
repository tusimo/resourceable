<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository\Cache;

use Hyperf\Utils\Arr;
use Tusimo\Resource\Utils\Table;
use Tusimo\Resource\Entity\CacheValue;
use Tusimo\Resource\Utils\SwooleTableManager;
use Tusimo\Resource\Contract\ResourceCacheAble;

class SwooleTableCache extends AbstractCache implements ResourceCacheAble
{
    public const CACHE_KEY = 'resource-cache:';

    protected Table $table;

    public function __construct(string $resourceName, string $keyName = 'id', string $prefix = '')
    {
        parent::__construct($resourceName, $keyName, $prefix);
        $this->table = SwooleTableManager::getTable(self::CACHE_KEY);
    }

    /**
     * Get resource by id.
     *
     * @param int|string $id
     */
    public function getResourceCache($id, array $select = []): CacheValue
    {
        $id = $this->getCacheKey($id);

        $result = $this->table->get($id);
        if ($result === false) {
            return new CacheValue(null, false, $this->getKeyName());
        }
        if ($this->shouldSelect($select)) {
            $result = Arr::only($result, array_merge([$this->getKeyName()], $select));
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

        return $this->table->del($id);
    }

    /**
     * Delete cache by resource ids.
     *
     * @return mixed
     */
    public function deleteResourcesCache(array $ids)
    {
        foreach ($ids as $id) {
            $this->deleteResourceCache($id);
        }
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

        $this->table->setWithExpire($id, $resource, $ttl);
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
