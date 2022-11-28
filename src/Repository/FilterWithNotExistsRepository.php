<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Query\Query;
use Hyperf\Paginator\LengthAwarePaginator;
use Tusimo\Resource\Contract\FilterContract;
use Tusimo\Resource\Contract\ResourceCleanAble;
use Tusimo\Resource\Contract\RepositoryProxyAble;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

/**
 * BloomFilterRepository
 * This filter will store the not exists data in the filter.
 */
class FilterWithNotExistsRepository extends Repository implements ResourceCleanAble, RepositoryProxyAble
{
    /**
     * Get Real Repo.
     *
     * @var ResourceRepositoryContract
     */
    protected $repository;

    protected bool $withoutFilter = false;

    protected FilterContract $filter;

    /**
     * Clean resource when remote resource changed.
     */
    protected bool $shouldClean = true;

    public function __construct(
        ResourceRepositoryContract $repository,
        string $resourceName,
        FilterContract $filter = null,
        string $keyName = 'id'
    ) {
        $this->repository = $repository;
        $this->resourceName = $resourceName;
        $this->filter = $filter;
        $this->keyName = $keyName;
        $this->getFilter()->setBucket('filter:nexists:' . $this->getResourceName() . ':' . $this->getKeyName());
    }

    public function withoutFilter()
    {
        $this->withoutFilter = true;
        return $this;
    }

    public function withFilter()
    {
        $this->withoutFilter = false;
        return $this;
    }

    public function shouldFilter(): bool
    {
        return ! $this->withoutFilter;
    }

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        if (! $this->shouldFilter()) {
            return $this->getRepository()->get($id, $select, $with);
        }

        $exists = $this->getFilter()->exists($id);
        if ($exists) {
            return [];
        }
        $data = $this->getRepository()->get($id, $select, $with);
        if (empty($data)) {
            $this->getFilter()->addItem($id);
        }
        return $data;
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        if (! $this->shouldFilter()) {
            return $this->getRepository()->getByIds($ids, $select, $with);
        }

        $notExistsList = [];
        foreach ($ids as $id) {
            $notExists = $this->getFilter()->exists($id);
            if ($notExists) {
                $notExistsList[] = $id;
            }
        }
        $checkedList = array_values(array_diff($ids, $notExistsList));
        if (empty($checkedList)) {
            return [];
        }
        $result = $this->getRepository()->getByIds($checkedList, $select, $with);
        if (count($checkedList) == count($result)) {
            return $result;
        }
        // get the missing resources and set to the filter
        $shouldSetBackToFilterList = [];
        $shouldSetBackToFilterList = collect($checkedList)->diff(collect($result)->pluck($this->getKeyName()))->all();
        if (! empty($shouldSetBackToFilterList)) {
            $this->getFilter()->addItems($shouldSetBackToFilterList);
        }
        return $result;
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        if (! $this->shouldFilter()) {
            return $this->getRepository()->add($resource);
        }

        $addedResource = $this->getRepository()->add($resource);
        if ($addedResource) {
            $this->getFilter()->removeItem($addedResource[$this->getKeyName()]);
        }
        return $addedResource;
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        if (! $this->shouldFilter()) {
            return $this->getRepository()->batchAdd($resources);
        }

        $addedResources = $this->getRepository()->batchAdd($resources);

        $this->getFilter()->removeItems(collect($addedResources)->pluck($this->getKeyName())->all());
        return $addedResources;
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        return $this->getRepository()->update($id, $resource);
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        return $this->getRepository()->batchUpdate($resources);
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        if (! $this->shouldFilter()) {
            return $this->getRepository()->delete($id);
        }

        $this->getFilter()->addItem($id);
        return $this->getRepository()->delete($id);
    }

    /**
     * Batch delete Resource and return the num that deleted.
     */
    public function deleteByIds(array $ids): int
    {
        if (! $this->shouldFilter()) {
            return $this->getRepository()->deleteByIds($ids);
        }

        $this->getFilter()->addItems($ids);
        return $this->getRepository()->deleteByIds($ids);
    }

    /**
     * Get Resource Paginator.
     *
     * @return LengthAwarePaginator
     */
    public function list(Query $query)
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        return $this->getRepository()->list($query);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        return $this->getRepository()->getByQuery($query);
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
     * Delete cache by resource id.
     *
     * @param int|string $id
     *
     * @return mixed
     */
    public function deleteResourceCache($id)
    {
        $this->getFilter()->addItem($id);
    }

    /**
     * Delete cache by resource ids.
     *
     * @return mixed
     */
    public function deleteResourcesCache(array $ids)
    {
        return $this->getFilter()->addItems($ids);
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

    /**
     * Get the value of filter.
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set the value of filter.
     *
     * @param mixed $filter
     * @return self
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }
}
