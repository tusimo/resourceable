<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Query\Query;
use Hyperf\DbConnection\Db;
use Tusimo\Query\QueryItem;
use Hyperf\Database\Query\Builder;

class SubDbTableRepository extends DbRepository
{
    /**
     * 根据哪个字段进行分库分表.
     */
    protected string $subKey = 'id';

    /**
     * 分表的函数.
     */
    protected $subTableResolver;

    /**
     * 分库的函数.
     */
    protected $subDbResolver;

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        $this->throwExceptionWhenKeyNotMatched();

        $builder = $this->getSubBuilder($id);
        return $this->getWithExistsBuilder($builder, $id, $select, $with);
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        $select = $this->getParsedSelect($select);

        $this->throwExceptionWhenKeyNotMatched();

        $builders = $this->getSubBuilders($ids);

        $resources = [];
        foreach ($builders as $item) {
            $builder = $item['builder'];
            $keys = $item['keys'];
            $resources = array_merge($resources, $this->getByIdsWithExistsBuilder($builder, $keys, $select, $with));
        }
        return $resources;
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        if (! isset($resource[$this->getSubKey()])) {
            return $this->throwWhenMissingSubKeyData();
        }
        $builder = $this->getSubBuilder($resource[$this->getSubKey()]);
        return $this->addWithExistsBuilder($builder, $resource);
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        $this->throwExceptionWhenKeyNotMatched();
        $builder = $this->getSubBuilder($id);
        return $this->updateWithExistsBuilder($builder, $id, $resource);
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        $this->throwExceptionWhenKeyNotMatched();
        $builder = $this->getSubBuilder($id);
        return $this->deleteWithExistsBuilder($builder, $id);
    }

    /**
     * Batch delete Resource.
     */
    public function deleteByIds(array $ids): int
    {
        $builders = $this->getSubBuilders($ids);

        $result = 0;
        foreach ($builders as $item) {
            $builder = $item['builder'];
            $keys = $item['keys'];
            $result += $this->deleteWithExistsBuilder($builder, $keys);
        }
        return $result;
    }

    /**
     * Get Resource Paginator.
     *
     * @return \Hyperf\Paginator\LengthAwarePaginator
     */
    public function list(Query $query)
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        $queryItem = $this->getQueryItemWithSubKeyFilter($query);
        if ($queryItem) {
            $builder = $this->getSubBuilder($queryItem->getValue());
            return $this->listWithExistsBuilder($builder, $query);
        }
        return $this->throwWhenMissingSubKeyData();
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        $query->select($this->getParsedSelect($query->getQuerySelect()->getSelects()));

        $queryItem = $this->getQueryItemWithSubKeyFilter($query);
        if ($queryItem) {
            $builder = $this->getSubBuilder($queryItem->getValue());
            return $this->getByQueryWithExistsBuilder($builder, $query);
        }
        return $this->throwWhenMissingSubKeyData();
    }

    public function aggregate(Query $query): array
    {
        $queryItem = $this->getQueryItemWithSubKeyFilter($query);
        if ($queryItem) {
            $builder = $this->getSubBuilder($queryItem->getValue());
            return $this->aggregateWithExistsBuilder($builder, $query);
        }
        return $this->throwWhenMissingSubKeyData();
    }

    /**
     * Get 分表的函数.
     */
    public function getSubTableResolver()
    {
        return $this->subTableResolver;
    }

    /**
     * Set 分表的函数.
     *
     * @param mixed $subTableResolver
     * @return self
     */
    public function setSubTableResolver($subTableResolver)
    {
        $this->subTableResolver = $subTableResolver;

        return $this;
    }

    /**
     * Get 根据哪个字段进行分库分表.
     */
    public function getSubKey()
    {
        return $this->subKey;
    }

    /**
     * Set 根据哪个字段进行分库分表.
     *
     * @param mixed $subKey
     * @return self
     */
    public function setSubKey($subKey)
    {
        $this->subKey = $subKey;

        return $this;
    }

    /**
     * Get 分库的函数.
     */
    public function getSubDbResolver()
    {
        return $this->subDbResolver;
    }

    /**
     * Set 分库的函数.
     *
     * @param mixed $subDbResolver
     * @return self
     */
    public function setSubDbResolver($subDbResolver)
    {
        $this->subDbResolver = $subDbResolver;

        return $this;
    }

    /**
     * getQueryItemWithSubKeyFilter.
     * @return QueryItem
     * @throws \RuntimeException
     */
    protected function getQueryItemWithSubKeyFilter(Query $query)
    {
        $queryItems = $query->getResourceQueryItems();
        $find = null;
        foreach ($queryItems as $queryItem) {
            if ($queryItem->getName() === $this->getSubKey() && $queryItem->isOperation('eq')) {
                $find = $queryItem;
                break;
            }
        }
        if ($find) {
            return $find;
        }
        return $this->throwWhenMissingSubKeyData();
    }

    /**
     * get builders
     * [
     * 'connectionName-tableName' => [
     *      'builder' => $builder,
     *      'keys' => [],
     *  ]
     * ].
     */
    protected function getSubBuilders(array $keys): array
    {
        $builders = [];
        foreach ($keys as $key) {
            $subTable = $this->getSubTable($key);
            $connectionName = $this->getSubConnectionNameByKey($key);
            $index = $connectionName . '-' . $subTable;
            $builders[$index]['keys'][] = $key;
            if (! isset($builders[$index]['builder'])) {
                $builders[$index]['builder'] = $this->getSubBuilder($key);
            }
        }
        return $builders;
    }

    protected function getSubBuilder($key): Builder
    {
        return Db::connection($this->getSubConnectionNameByKey($key))->table($this->getSubTable($key));
    }

    protected function getSubConnectionNameByKey($key): string
    {
        if (is_null($this->getSubDbResolver())) {
            return $this->getConnectionName();
        }
        return call_user_func($this->getSubDbResolver(), $key, $this->getConnectionName());
    }

    protected function getSubConnectionNamesByKeys(array $keys)
    {
        $connectionNames = [];
        foreach ($keys as $key) {
            $connectionName = $this->getSubConnectionNameByKey($key);
            $connectionNames[$connectionName][] = $key;
        }
        return $connectionNames;
    }

    protected function getSubTablesByKeys(array $keys)
    {
        $tables = [];
        foreach ($keys as $key) {
            $table = $this->getSubTable($key);
            $tables[$table][] = $key;
        }
        return $tables;
    }

    protected function getSubTable($key)
    {
        if ($this->subTableResolver) {
            return call_user_func($this->subTableResolver, $key, $this->getTable());
        }
        return $this->getTable();
    }

    protected function throwExceptionWhenKeyNotMatched()
    {
        $this->throwIfMissingPropertyException();
        if ($this->getKeyName() !== $this->getSubKey()) {
            throw new \RuntimeException('The key name is not matched with sub table key.');
        }
    }

    protected function throwIfMissingPropertyException()
    {
        if (! $this->getKeyName()) {
            throw new \RuntimeException('The key name is empty.');
        }
        if (! $this->getSubKey()) {
            throw new \RuntimeException('The sub table key is empty.');
        }
        if (! $this->getSubTableResolver()) {
            throw new \RuntimeException('The sub table resolver is empty.');
        }
    }

    protected function throwWhenMissingSubKeyData()
    {
        throw new \RuntimeException('missing the sub key data.');
    }

    /**
     * 取模分库分表算法.
     *
     * @param int $mod
     *
     * @return \Closure
     */
    protected function modResolver($mod = 100)
    {
        return function ($key, $prefix) use ($mod) {
            $subKey = ($key % $mod);
            if ($subKey < 10) {
                $subKey = '0' . $subKey;
            }
            return $prefix . $subKey;
        };
    }

    /**
     * Md5 取前几位分库分表算法.
     *
     * @param int $length
     *
     * @return \Closure
     */
    protected function md5Resolver($length = 1)
    {
        return function ($key, $prefix) use ($length) {
            $key = strval($key);
            $subKey = substr(md5($key), 0, $length);
            return $prefix . $subKey;
        };
    }
}
