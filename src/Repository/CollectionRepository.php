<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Query\Query;
use Tusimo\Resource\Resource;
use Tusimo\Resource\Utils\IdGenerator;
use Tusimo\Resource\Utils\MemoryCollection;
use Tusimo\Resource\Utils\MemoryCollectionManager;

class CollectionRepository extends Repository
{
    protected MemoryCollection $collection;

    protected string $keyType = 'int';

    protected IdGenerator $idGenerator;

    public function __construct(string $resourceName, string $keyName = 'id', string $keyType = 'int')
    {
        $this->keyName = $keyName;
        $this->resourceName = $resourceName;
        $this->keyType = $keyType;
        $this->setCollection(MemoryCollectionManager::getCollection($resourceName));
        $this->setIdGenerator(new IdGenerator($resourceName, $keyType));
    }

    public function getCollection(): MemoryCollection
    {
        return $this->collection;
    }

    public function setCollection(MemoryCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        $query = query()->where($this->getKeyName(), $id)
            ->select($select)->with($with);
        $result = $this->getCollectionByQueryWithoutPage($this->getCollection(), $query)
            ->first();
        if (empty($result)) {
            return [];
        }
        return $result;
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        $query = query()->whereIn($this->getKeyName(), $ids)
            ->select($select)->with($with);
        return $this->getCollectionByQueryWithoutPage($this->getCollection(), $query)
            ->toArray();
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        $result = $resource + $this->virtualIdPair();
        $this->getCollection()->push($result);
        return $result;
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        $results = [];
        foreach ($resources as $resource) {
            $results[] = $this->add($resource);
        }
        return $results;
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        $oldResource = $this->get($id);

        if (empty($oldResource)) {
            return [];
        }

        $this->getCollection()->updateBy($this->getKeyName(), $id, $resource);

        return $this->get($id);
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        $keyName = $this->getKeyName();
        $results = [];
        foreach ($resources as $resource) {
            $results[] = $this->update($resource[$keyName], $resource);
        }
        return $results;
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        $this->getCollection()->deleteBy($this->getKeyName(), $id);
        return true;
    }

    /**
     * Batch delete Resource.
     */
    public function deleteByIds(array $ids): int
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }
        return count($ids);
    }

    /**
     * Get Resource Paginator.
     *
     * @return \Hyperf\Paginator\LengthAwarePaginator
     */
    public function list(Query $query)
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        $collection = $this->getCollection();
        $collection = $this->getCollectionByQueryWithoutPage($collection, $query);
        // handle per_page
        $perPage = 10;
        $page = 1;

        if ($query->hasQueryPagination()) {
            $queryPagination = $query->getQueryPagination();
            $perPage = $queryPagination->getPerPage() ?? 10;
            $page = $queryPagination->getPage() ?? 1;
        }
        return $collection->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        $collection = $this->getCollection();

        $collection = $this->getCollectionByQueryWithoutPage($collection, $query);

        if ($query->hasQuerySeek()) {
            if ($query->getQuerySeek()->hasOffset()) {
                $collection = $collection
                    ->offset($query->getQuerySeek()->getOffset());
            }
            if ($query->getQuerySeek()->hasLimit()) {
                $collection = $collection
                    ->limit($query->getQuerySeek()->getLimit());
            }
        }
        return $collection->toArray();
    }

    /**
     * Get Resource aggregate By Query.
     *
     * @return array
     *               [
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
        if (! $query->hasQueryAggregate()) {
            return [];
        }
        $result = [];
        $collection = $this->getCollectionByQueryWithoutPage(
            $this->getCollection(),
            $query
        );
        foreach ($query->getQueryAggregate()->getAggregates() as $aggregate => $columns) {
            foreach ($columns as $column) {
                if ($aggregate == 'count') {
                    if ($column == '*' || $column == 1) {
                        $result[$aggregate][$column] = $collection->count();
                    } else {
                        $result[$aggregate][$column] = $collection->filter(
                            function ($value) use ($column) {
                                return ! is_null($value[$column] ?? null);
                            }
                        )->count();
                    }
                } else {
                    $result[$aggregate][$column] = $collection
                        ->{$aggregate}($column);
                }
            }
        }
        return $result;
    }

    /**
     * Get the value of idGenerator.
     */
    public function getIdGenerator()
    {
        return $this->idGenerator;
    }

    /**
     * Set the value of idGenerator.
     *
     * @param mixed $idGenerator
     * @return self
     */
    public function setIdGenerator($idGenerator)
    {
        $this->idGenerator = $idGenerator;

        return $this;
    }

    /**
     * Get the value of keyType.
     */
    public function getKeyType()
    {
        return $this->keyType;
    }

    /**
     * Set the value of keyType.
     *
     * @param mixed $keyType
     * @return self
     */
    public function setKeyType($keyType)
    {
        $this->keyType = $keyType;

        return $this;
    }

    protected function virtualId()
    {
        return $this->getIdGenerator()->getNextId();
    }

    protected function virtualIdPair(): array
    {
        return [
            $this->getKeyName() => $this->virtualId(),
        ];
    }

    protected function getCollectionByQueryWithoutPage(MemoryCollection $collection, Query $query): MemoryCollection
    {
        $collection = $this->filterCollectionByQueryItems(
            $collection,
            $query->getResourceQueryItems()
        );

        // attach select
        if ($query->hasQuerySelect()) {
            $selects = $query->getQuerySelect()->getSelects();
            $collection = $collection->select($selects);
        }

        // attach order
        if ($query->hasQueryOrderBy()) {
            $collection = $collection->orderBy(
                $query->getQueryOrderBy()->getOrderBy() ?? 'created_at',
                $query->getQueryOrderBy()->getOrder() ?? 'desc'
            );
        }
        return $collection;
    }

    protected function filterCollectionByQueryItems(MemoryCollection $collection, array $queryItems): MemoryCollection
    {
        foreach ($queryItems as $queryItem) {
            switch ($queryItem->getOperation()) {
                case 'eq':
                    $collection = $collection->where($queryItem->getName(), $queryItem->getValue());
                    break;
                case 'not_eq':
                    $collection = $collection->where($queryItem->getName(), '!=', $queryItem->getValue());
                    break;
                case 'gt':
                    $collection = $collection->where($queryItem->getName(), '>', $queryItem->getValue());
                    break;
                case 'gte':
                    $collection = $collection->where($queryItem->getName(), '>=', $queryItem->getValue());
                    break;
                case 'lt':
                    $collection = $collection->where($queryItem->getName(), '<', $queryItem->getValue());
                    break;
                case 'lte':
                    $collection = $collection->where($queryItem->getName(), '<=', $queryItem->getValue());
                    break;
                case 'in':
                    $collection = $collection->whereIn($queryItem->getName(), $queryItem->getValue());
                    break;
                case 'not_in':
                    $collection = $collection->whereNotIn($queryItem->getName(), $queryItem->getValue());
                    break;
                case 'between':
                    $collection = $collection->whereBetween($queryItem->getName(), $queryItem->getValue());
                    break;
                case 'not_between':
                    $collection = $collection->whereNotBetween($queryItem->getName(), $queryItem->getValue());
                    break;
                case 'like':
                    $collection = $collection->whereLike($queryItem->getName(), $queryItem->getValue());
                    break;
                case 'null':
                    $collection = $collection->whereNull($queryItem->getName());
                    break;
                case 'not_null':
                    $collection = $collection->whereNotNull($queryItem->getName());
                    break;
                default:
                    $collection = $collection->where($queryItem->getName(), $queryItem->getOperation(), $queryItem->getValue());
            }
        }
        return $collection;
    }
}
