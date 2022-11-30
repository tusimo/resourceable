<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Hyperf\Utils\Arr;
use Tusimo\Restable\Query;
use Tusimo\Resource\Contract\ResourceCacheAble;
use Tusimo\Resource\Contract\ResourceCleanAble;
use Tusimo\Resource\Contract\RepositoryProxyAble;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class MixCacheRepository extends ProxyRepository implements ResourceCleanAble, RepositoryProxyAble
{
    public const VISUAL_ID = '_visual_id';

    public const REAL_ID = '_real_id';

    // cache instance for store mix key index
    protected ResourceCacheAble $cache;

    /**
     * Ttl in seconds.
     */
    protected int $ttl;

    /**
     * Mix Keys
     * All Keys should be unique in database.
     */
    protected array $mixKeys = [];

    public function __construct(
        ResourceRepositoryContract $repository,
        string $resourceName,
        ResourceCacheAble $cache = null,
        string $keyName = 'id',
        int $ttl = 3600,
        array $mixKeys = []
    ) {
        $this->repository = $repository;
        $this->resourceName = $resourceName;
        $this->cache = $cache;
        $this->keyName = $keyName;
        $this->ttl = $ttl;
        $this->mixKeys = $mixKeys;
        // auto set cache
        $this->getCache()->setKeyName(self::VISUAL_ID);
        $this->getCache()->setResourceName($this->getResourceName() . ':idx');
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        $resource = parent::add($resource);
        if ($this->hasMixKeys() && ! empty($resource)) {
            $this->setResourceMixCache($resource);
        }
        return $resource;
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        $resources = parent::batchAdd($resources);
        if ($this->hasMixKeys() && ! empty($resources)) {
            $this->setResourcesMixCache($resources);
        }
        return $resources;
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        $resource = parent::update($id, $resource);
        if ($this->hasMixKeys() && ! empty($resource)) {
            $this->setResourceMixCache($resource);
        }
        return $resource;
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        $resources = parent::batchUpdate($resources);
        if ($this->hasMixKeys() && ! empty($resources)) {
            $this->setResourcesMixCache($resources);
        }
        return $resources;
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        if ($this->hasMixKeys()) {
            $resource = $this->get($id);
            if (! empty($resource)) {
                $this->deleteResourceMixCache($resource);
            }
        }
        return parent::delete($id);
    }

    /**
     * Batch delete Resource and return the num that deleted.
     */
    public function deleteByIds(array $ids): int
    {
        if ($this->hasMixKeys()) {
            $resources = $this->getByIds($ids);
            if (! empty($resources)) {
                $this->deleteResourcesMixCache($resources);
            }
        }
        return parent::deleteByIds($ids);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        // optimize for query for unique key
        $queryItems = $query->getResourceQueryItems();
        // if query contains all mix keys ,which should be optimized
        $visualKeys = [];
        // current only handle the eq situations
        // @todo add in operation handle
        foreach ($queryItems as $queryItem) {
            if (in_array($queryItem->getName(), $this->getMixKeys())) {
                if ($queryItem->isOperation('eq')) {
                    $visualKeys[$queryItem->getName()] = $queryItem->getValue();
                }
            }
        }
        if (count($visualKeys) == count($this->getMixKeys())) {
            // all mix keys are queried
            $id = $this->getResourceMixCacheRealId($visualKeys);
            // pass to parent get the resources
            $resource = $this->get($id);
            if (empty($resource)) {
                // delete the cache index
                $this->deleteResourceMixCache($visualKeys);
            }
            return $resource;
        }
        return parent::getByQuery($query);
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
        $this->getCache()->deleteResourceCache($id);
    }

    /**
     * Delete cache by resource ids.
     *
     * @return mixed
     */
    public function deleteResourcesCache(array $ids)
    {
        return $this->getCache()->deleteResourcesCache($ids);
    }

    public function getMixKeys(): array
    {
        return $this->mixKeys;
    }

    public function setMixKeys(array $mixKeys): self
    {
        $this->mixKeys = $mixKeys;
        return $this;
    }

    public function hasMixKeys(): bool
    {
        return ! empty($this->getMixKeys());
    }

    public function getMixCacheKeyName(array $keys): string
    {
        $cacheKey = 'mix:';
        foreach ($this->getMixKeys() as $key) {
            $cacheKey .= ($key . ':' . $keys[$key]) . ':';
        }
        return rtrim($cacheKey, ':');
    }

    public function getResourceMixCacheKeyName(array $resource): string
    {
        $keys = Arr::only($resource, $this->getMixKeys());
        return $this->getMixCacheKeyName($keys);
    }

    public function getResourceMixCacheValue(array $resource): array
    {
        return [
            self::REAL_ID => $resource[$this->getKeyName()],
            self::VISUAL_ID => $this->getResourceMixCacheKeyName($resource),
        ];
    }

    public function getResourceMixCacheRealId(array $resourceKeys): string
    {
        $cacheValue = $this->getCache()->getResourceCache($this->getResourceMixCacheKeyName($resourceKeys));
        if ($cacheValue->isExists()) {
            return $cacheValue->getValue()[self::REAL_ID] ?? '';
        }
        return '';
    }

    public function getResourcesMixCacheRealIds(array $resourcesKeys): array
    {
        $keys = [];
        foreach ($resourcesKeys as $key) {
            $keys[] = $this->getResourceMixCacheKeyName($key);
        }
        $cacheValues = $this->getCache()->getResourcesCache($keys);
        $resourceIds = [];
        foreach ($cacheValues as $cacheValue) {
            if ($cacheValue->isExists()) {
                $id = $cacheValue->getValue()[self::REAL_ID] ?? '';
                if ($id !== '') {
                    $resourceIds[] = $id;
                }
            }
        }
        return $resourceIds;
    }

    public function setResourceMixCache(array $resource)
    {
        $this->getCache()->setResourceCache(
            $this->getResourceMixCacheKeyName($resource),
            $this->getResourceMixCacheValue($resource),
            $this->getRandomTtl()
        );
    }

    public function setResourcesMixCache(array $resources)
    {
        $virtualResources = [];
        foreach ($resources as $resource) {
            $virtualResources[] = $this->getResourceMixCacheValue($resource);
        }
        $this->getCache()->setResourcesCache($virtualResources, $this->getRandomTtl());
    }

    public function deleteResourceMixCache(array $resource)
    {
        $this->getCache()->deleteResourceCache($this->getResourceMixCacheKeyName($resource));
    }

    public function deleteResourcesMixCache(array $resources)
    {
        $virtualResourceIds = [];
        foreach ($resources as $resource) {
            $virtualResourceIds[] = $this->getResourceMixCacheKeyName($resource);
        }
        $this->getCache()->deleteResourcesCache($virtualResourceIds);
    }

    /**
     * clean resource.
     */
    public function shouldClean(): bool
    {
        return true;
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

    /**
     * Get expire Seconds for resource.
     *
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set expire Seconds for resource.
     *
     * @param int $ttl Expire Seconds for resource
     *
     * @return self
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    protected function getRandomTtl()
    {
        return rand($this->getTtl() - 20, $this->getTtl() + 20);
    }
}
