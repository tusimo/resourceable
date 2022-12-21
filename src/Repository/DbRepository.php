<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Restable\Query;
use Hyperf\DbConnection\Db;
use Tusimo\Restable\QueryItem;
use Hyperf\Database\Query\Builder;
use Hyperf\DbConnection\Connection;
use Hyperf\Utils\Contracts\Arrayable;

class DbRepository extends Repository
{
    protected string $table;

    /**
     * Connection name.
     */
    protected string $connectionName;

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        $builder = $this->getBuilder();
        return $this->getWithExistsBuilder($builder, $id, $select, $with);
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        $builder = $this->getBuilder();
        return $this->getByIdsWithExistsBuilder($builder, $ids, $select, $with);
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        $builder = $this->getBuilder();
        return $this->addWithExistsBuilder($builder, $resource);
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        $collection = [];
        foreach ($resources as $resource) {
            $collection[] = $this->add($resource);
        }
        return $collection;
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        $builder = $this->getBuilder();
        return $this->updateWithExistsBuilder($builder, $id, $resource);
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        $collection = [];
        foreach ($resources as $resource) {
            $collection[] = $this->update($resource[$this->getKeyName()], $resource);
        }
        return $collection;
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        $builder = $this->getBuilder();
        return $this->deleteWithExistsBuilder($builder, $id);
    }

    /**
     * Batch delete Resource.
     */
    public function deleteByIds(array $ids): int
    {
        $builder = $this->getBuilder();
        return $this->deleteByIdsWithExistsBuilder($builder, $ids);
    }

    /**
     * Get Resource Paginator.
     *
     * @return \Hyperf\Paginator\LengthAwarePaginator
     */
    public function list(Query $query)
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        $builder = $this->getBuilder();

        return $this->listWithExistsBuilder($builder, $query);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        $builder = $this->getBuilder();
        return $this->getByQueryWithExistsBuilder($builder, $query);
    }

    /**
     * Get the value of table.
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set the value of table.
     *
     * @param mixed $table
     * @return self
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
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
        $builder = $this->getBuilder();
        return $this->aggregateWithExistsBuilder($builder, $query);
    }

    /**
     * Get connection name.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * Set connection name.
     *
     * @param string $connectionName connection name
     *
     * @return self
     */
    public function setConnectionName(string $connectionName)
    {
        $this->connectionName = $connectionName;

        return $this;
    }

    protected function getConnection()
    {
        return Db::connection($this->connectionName);
    }

    protected function getWithExistsBuilder(Builder $builder, $id, array $select = [], array $with = []): array
    {
        $requestQuery = query()->select($select)
            ->with($with)->where($this->getKeyName(), $id);
        $result = $this->attachQuery($builder, $requestQuery)->first();
        if (empty($result)) {
            return [];
        }
        if ($result instanceof Arrayable) {
            return $result->toArray();
        }
        return $result;
    }

    protected function getByIdsWithExistsBuilder(Builder $builder, array $ids, array $select = [], array $with = []): array
    {
        $requestQuery = query()->select($select)
            ->with($with);
        $query = $this->attachQuery($builder, $requestQuery);

        return $query->whereIn($this->getKeyName(), $ids)->get()->toArray();
    }

    protected function addWithExistsBuilder(Builder $builder, array $resource): array
    {
        $id = $builder->insertGetId($resource);
        if (is_int($id)) {
            return $resource + [
                $this->getKeyName() => $id,
            ];
        }
        return [];
    }

    protected function updateWithExistsBuilder(Builder $builder, $id, array $resource): array
    {
        $result = $builder->where($this->getKeyName(), $id)
            ->update($resource);
        if ($result) {
            return $resource + [
                $this->getKeyName() => $id,
            ];
        }
        return [];
    }

    protected function deleteWithExistsBuilder(Builder $builder, $id): bool
    {
        return (bool) $builder->where($this->getKeyName(), $id)->delete();
    }

    protected function deleteByIdsWithExistsBuilder(Builder $builder, array $ids): int
    {
        return $builder->whereIn($this->getKeyName(), $ids)
            ->delete();
    }

    protected function listWithExistsBuilder(Builder $builder, Query $query)
    {
        /**
         * @var Builder $dbQuery
         */
        $dbQuery = $this->attachQuery($builder, $query);

        $perPage = 10;
        $page = 1;

        if ($query->hasQueryPagination()) {
            $queryPagination = $query->getQueryPagination();
            $perPage = $queryPagination->getPerPage() ?? 10;
            $page = $queryPagination->getPage() ?? 1;
        }
        $columns = $query->getQuerySelect()->getResourceSelect();

        return $dbQuery->paginate($perPage, $columns, 'page', $page);
    }

    protected function getByQueryWithExistsBuilder(Builder $builder, Query $query): array
    {
        /**
         * @var Builder $dbQuery
         */
        $dbQuery = $this->attachQuery($builder, $query);

        if ($query->hasQuerySeek()) {
            if ($query->getQuerySeek()->hasOffset()) {
                $dbQuery->offset($query->getQuerySeek()->getOffset());
            }
            if ($query->getQuerySeek()->hasLimit()) {
                $dbQuery->limit($query->getQuerySeek()->getLimit());
            }
        }
        return $dbQuery->get()->toArray();
    }

    protected function aggregateWithExistsBuilder(Builder $builder, Query $query): array
    {
        if (! $query->hasQueryAggregate()) {
            return [];
        }

        /**
         * @var Builder $dbQuery
         */
        $dbQuery = $this->attachQueryItems(
            $builder,
            $query->getResourceQueryItems()
        );

        $queryAggregate = $query->getQueryAggregate();

        foreach ($queryAggregate->getAggregates() as $aggregate => $columns) {
            foreach ($columns as $column) {
                $dbQuery->selectRaw("{$aggregate}({$column}) as '{$aggregate}.{$column}'");
            }
        }
        // format result
        $result = $dbQuery->first();
        if (empty($result)) {
            return [];
        }
        $aggregates = [];
        foreach ($result as $columnExpression => $value) {
            [$aggregate, $column] = explode('.', $columnExpression);
            $aggregates[$aggregate][$column] = $value;
        }
        return $aggregates;
    }

    /**
     * Get Builder.
     *
     * @return Builder
     */
    protected function getBuilder()
    {
        return $this->getConnection()->table($this->table);
    }

    /**
     * Attach QueryItems  to db Query.
     *
     * @param Builder $dbQuery
     *
     * @return Builder
     */
    protected function attachQueryItems($dbQuery, array $queryItems)
    {
        foreach ($queryItems as $queryItem) {
            $dbQuery = $this->attachQueryItem($dbQuery, $queryItem);
        }
        return $dbQuery;
    }

    /**
     * Attach QueryItem to db Query.
     *
     * @param Builder $dbQuery
     *
     * @return Builder
     */
    protected function attachQueryItem($dbQuery, QueryItem $queryItem)
    {
        switch ($queryItem->getOperation()) {
            case 'eq':
                $dbQuery->where($queryItem->getName(), $queryItem->getValue());
                break;
            case 'not_eq':
                $dbQuery->where($queryItem->getName(), '!=', $queryItem->getValue());
                break;
            case 'gt':
                $dbQuery->where($queryItem->getName(), '>', $queryItem->getValue());
                break;
            case 'gte':
                $dbQuery->where($queryItem->getName(), '>=', $queryItem->getValue());
                break;
            case 'lt':
                $dbQuery->where($queryItem->getName(), '<', $queryItem->getValue());
                break;
            case 'lte':
                $dbQuery->where($queryItem->getName(), '<=', $queryItem->getValue());
                break;
            case 'in':
                $dbQuery->whereIn($queryItem->getName(), $queryItem->getValue());
                break;
            case 'not_in':
                $dbQuery->whereNotIn($queryItem->getName(), $queryItem->getValue());
                break;
            case 'between':
                $dbQuery->whereBetween($queryItem->getName(), $queryItem->getValue());
                break;
            case 'not_between':
                $dbQuery->whereNotBetween($queryItem->getName(), $queryItem->getValue());
                break;
            case 'like':
                $dbQuery->where($queryItem->getName(), 'like', $queryItem->getValue() . '%');
                break;
            case 'null':
                $dbQuery->whereNull($queryItem->getName());
                break;
            case 'not_null':
                $dbQuery->whereNotNull($queryItem->getName());
                break;
            default:
                $dbQuery->where($queryItem->getName(), $queryItem->getOperation(), $queryItem->getValue());
        }
        return $dbQuery;
    }

    /**
     * Attach Query to db Query.
     *
     * @return Builder
     */
    protected function attachQuery(Builder $query, Query $requestQuery)
    {
        // attach select
        if ($requestQuery->hasQuerySelect()) {
            $selects = $requestQuery->getQuerySelect()->getResourceSelect();
            $query->select(empty($selects) ? ['*'] : $selects);
        } else {
            $query->select(['*']);
        }

        // only attach parent where
        $query = $this->attachQueryItems(
            $query,
            $requestQuery->getResourceQueryItems()
        );
        // attach order by
        if ($requestQuery->hasQueryOrderBy()) {
            $query->orderBy(
                $requestQuery->getQueryOrderBy()->getOrderBy() ?? 'created_at',
                $requestQuery->getQueryOrderBy()->getOrder() ?? 'desc'
            );
        }
        return $query;
    }

    protected function attachSubQuery(Builder $query, Query $requestQuery)
    {
        // get subSelects if exists
        $subResourceSelect = optional($requestQuery->getQuerySelect())->getSubResourceSelect() ?? [];
        $subWhere = $requestQuery->getSubResourceQueryItems();
        // now we handle sub resource for the query

        if (! is_null($queryWith = $requestQuery->getQueryWith())) {
            // only use with should return sub resource
            $spreadWith = array_unique($queryWith->getSpreadWith());
            $finalWith = [];
            // set sub resource where
            foreach ($spreadWith as $with) {
                $queryItems = $subWhere[$with] ?? [];
                if (! empty($queryItems)) {
                    $finalWith[$with] = function ($subQuery) use ($with, $subResourceSelect, $queryItems) {
                        if (null !== ($subSelect = $subResourceSelect[$with] ?? null)) {
                            $subQuery->select($subSelect);
                        }
                        return $this->attachQueryItems($subQuery, $queryItems);
                    };
                } else {
                    if (null !== ($subSelect = $subResourceSelect[$with] ?? null)) {
                        $finalWith[$with] = function ($subQuery) use ($subSelect) {
                            $subQuery->select($subSelect);
                        };
                    }
                }
            }
            $query->with($finalWith);
        }
        return $query;
    }
}
