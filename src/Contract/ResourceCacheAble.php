<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Contract;

use Tusimo\Resource\Entity\CacheValue;

interface ResourceCacheAble extends ResourceCleanAble
{
    /**
     * Get resource by id.
     * @param int|string $id
     */
    public function getResourceCache($id, array $select = []): CacheValue;

    /**
     * Get Resources by ids.
     * @return CacheValue[]
     */
    public function getResourcesCache(array $ids, array $select = []): array;

    /**
     * Set resource cache.
     * @param int|string $id
     * @param mixed $resource
     */
    public function setResourceCache($id, $resource, int $ttl);

    /**
     * Set resources cache.
     */
    public function setResourcesCache(array $resources, int $ttl);
}
