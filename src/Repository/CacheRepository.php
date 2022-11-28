<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Swoole\Timer;
use Hyperf\Utils\Arr;
use Tusimo\Restable\Query;
use Hyperf\Paginator\LengthAwarePaginator;
use Tusimo\Resource\Contract\ResourceCacheAble;
use Tusimo\Resource\Contract\ResourceCleanAble;
use Tusimo\Resource\Contract\RepositoryProxyAble;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class CacheRepository extends Repository implements ResourceCleanAble, RepositoryProxyAble
{
    /**
     * Expire Seconds for resource.
     */
    protected int $ttl = 3600;

    /**
     * Clean resource when remote resource changed.
     */
    protected bool $shouldClean = true;

    /**
     * The cache instance.
     */
    protected ResourceCacheAble $cache;

    /**
     * Get Real Repo.
     *
     * @var ResourceRepositoryContract
     */
    protected $repository;

    protected bool $withoutCache = false;

    public function __construct(
        ResourceRepositoryContract $repository,
        string $resourceName,
        ResourceCacheAble $cache = null,
        string $keyName = 'id',
        int $ttl = 3600
    ) {
        $this->repository = $repository;
        $this->resourceName = $resourceName;
        $this->cache = $cache;
        $this->keyName = $keyName;
        $this->ttl = $ttl;
    }

    public function withoutCache()
    {
        $this->withoutCache = true;
        return $this;
    }

    public function withCache()
    {
        $this->withoutCache = false;
        return $this;
    }

    public function shouldCache(): bool
    {
        return ! $this->withoutCache;
    }

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);
        if (! $this->shouldCache()) {
            return $this->getRepository()->get($id, $select, $with);
        }

        $resource = [];
        $cacheValue = $this->getCache()->getResourceCache($id, $select);
        if (! $cacheValue->isExists()) {
            $resource = $this->getRepository()->get($id);
            $this->getCache()->setResourceCache($id, $resource, $this->getRandomTtl());
        } else {
            $resource = $cacheValue->getValue();
        }

        if (empty($resource)) {
            return [];
        }
        if ($this->shouldSelect($select)) {
            return Arr::only($resource, $select);
        }
        return $resource;
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        if (! $this->shouldCache()) {
            return $this->getRepository()->getByIds($ids, $select, $with);
        }

        if (empty($ids)) {
            return [];
        }
        $cachedResources = $this->getCache()->getResourcesCache($ids, $select);
        $hitResources = [];
        $missResources = [];
        $results = [];
        // divide cachedResources into two array
        foreach ($cachedResources as $id => $cachedResource) {
            if ($cachedResource->isExists()) {
                if (! empty($cachedResource->getValue())) {
                    $results[] = $cachedResource->getValue();
                    $hitResources[$id] = $cachedResource->getValue();
                }
            } else {
                $missResources[$id] = $id;
            }
        }
        if (! empty($missResources)) {
            // get all missed cache from repository
            $repoResources = $this->getRepository()->getByIds(array_keys($missResources));
            $repoResourceIds = collect($repoResources)->pluck($this->getKeyName());
            $missResourceIds = array_values(array_diff($missResources, $repoResourceIds->toArray()));
            // set missed resource to cache
            foreach ($missResourceIds as $id) {
                $this->getCache()->setResourceCache($id, [], $this->getRandomTtl());
            }
            if ($repoResources) {
                foreach ($repoResources as $repoResource) {
                    $hitResources[$repoResource[$this->getKeyName()]] = $repoResource;
                    $results[] = $repoResource;
                }
                $this->getCache()->setResourcesCache($hitResources, $this->getRandomTtl());
            }
        }

        $resourceCollection = collect($results);
        if ($this->shouldSelect($select)) {
            $collection = collect([]);
            $resourceCollection->filter()->values()->each(function ($resource) use ($collection, $select) {
                $collection->prepend(collect($resource)->only($select)->toArray());
            });
            return $collection->toArray();
        }
        return $resourceCollection->toArray();
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        if (! $this->shouldCache()) {
            return $this->getRepository()->add($resource);
        }

        $addedResource = $this->getRepository()->add($resource);
        if ($addedResource) {
            $this->getCache()->setResourceCache($addedResource[$this->getKeyName()], $addedResource, $this->getRandomTtl());
        }
        return $addedResource;
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        if (! $this->shouldCache()) {
            return $this->getRepository()->batchAdd($resources);
        }

        $addedResources = $this->getRepository()->batchAdd($resources);

        $this->getCache()->setResourcesCache($addedResources, $this->getRandomTtl());
        return $addedResources;
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        if (! $this->shouldCache()) {
            return $this->getRepository()->update($id, $resource);
        }

        $this->getCache()->deleteResourceCache($id);
        $updatedResource = $this->getRepository()->update($id, $resource);
        if ($updatedResource) {
            $this->setCacheExpiring($id);
        }
        return $updatedResource;
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        if (! $this->shouldCache()) {
            return $this->getRepository()->batchUpdate($resources);
        }

        $resourceKeys = collect($resources)->pluck($this->getKeyName());
        $this->getCache()->deleteResourcesCache($resourceKeys->toArray());
        $updatedResources = $this->getRepository()->batchUpdate($resources);
        if ($updatedResources) {
            $this->setCacheExpiring($resourceKeys->toArray());
        }
        return $updatedResources;
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        if (! $this->shouldCache()) {
            return $this->getRepository()->delete($id);
        }

        $this->deleteResourceCache($id);
        $result = $this->getRepository()->delete($id);
        if ($result) {
            $this->deleteResourceCache($id);
        }
        return $result;
    }

    /**
     * Batch delete Resource and return the num that deleted.
     */
    public function deleteByIds(array $ids): int
    {
        if (! $this->shouldCache()) {
            return $this->getRepository()->deleteByIds($ids);
        }

        $this->deleteResourcesCache($ids);
        $result = $this->getRepository()->deleteByIds($ids);
        if ($result) {
            $this->deleteResourcesCache($ids);
        }
        return $result;
    }

    /**
     * Get Resource Paginator.
     *
     * @return LengthAwarePaginator
     */
    public function list(Query $query)
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));
        return tap($this->getRepository()->list($query), function (LengthAwarePaginator $paginator) use ($query) {
            if ($this->shouldCache() && $query->getQuerySelect()->isSelectAll()) {
                $this->getCache()->setResourcesCache($paginator->items(), $this->getRandomTtl());
            }
        });
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));
        return tap($this->getRepository()->getByQuery($query), function ($resources) use ($query) {
            if ($this->shouldCache() && $query->getQuerySelect()->isSelectAll()) {
                $this->getCache()->setResourcesCache($resources, $this->getRandomTtl());
            }
        });
    }

    /**
     * Get Resource aggregate By Query.
     *
     * @return array [
     *               'count' => [
     *               'id' => 1,
     *               'score' => 3
     *               ],
     *               'sum' => [
     *               'money' => 10,
     *               'score' => 3
     *               ],
     *               ]
     */
    public function aggregate(Query $query): array
    {
        return $this->getRepository()->aggregate($query);
    }

    /**
     * Get get Real Repo.
     */
    public function getRepository(): ResourceRepositoryContract
    {
        return $this->repository;
    }

    /**
     * Set get Real Repo.
     *
     * @param ResourceRepositoryContract $repository Get Real Repo
     *
     * @return self
     */
    public function setRepository(ResourceRepositoryContract $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Get the cache instance.
     *
     * @return ResourceCacheAble
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Set the cache instance.
     *
     * @param ResourceCacheAble $cache The cache instance
     *
     * @return self
     */
    public function setCache(ResourceCacheAble $cache)
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

    /**
     * clean resource.
     */
    public function shouldClean(): bool
    {
        return $this->shouldClean;
    }

    /**
     * Set clean resource when remote resource changed.
     *
     * @return self
     */
    public function withClean()
    {
        $this->shouldClean = true;

        return $this;
    }

    public function withOutClean()
    {
        $this->shouldClean = false;

        return $this;
    }

    protected function getRandomTtl()
    {
        return rand($this->getTtl() - 20, $this->getTtl() + 20);
    }

    protected function setCacheExpiring($id)
    {
        Timer::after(500, function () use ($id) {
            if (is_array($id)) {
                $this->deleteResourcesCache($id);
            } else {
                $this->deleteResourceCache($id);
            }
        });
    }
}
