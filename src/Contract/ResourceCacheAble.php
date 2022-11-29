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

    /**
     * Get the value of keyName.
     */
    public function getKeyName(): string;

    /**
     * Set the value of keyName.
     *
     * @param mixed $keyName
     * @return self
     */
    public function setKeyName($keyName);

    /**
     * Get the value of resourceName.
     */
    public function getResourceName(): string;

    /**
     * Set the value of resourceName.
     *
     * @param mixed $resourceName
     * @return self
     */
    public function setResourceName($resourceName);

    /**
     * Get the value of prefix.
     */
    public function getPrefix(): string;

    /**
     * Set the value of prefix.
     *
     * @param mixed $prefix
     * @return self
     */
    public function setPrefix($prefix);
}
