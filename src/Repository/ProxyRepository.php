<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Restable\Query;
use Hyperf\Paginator\LengthAwarePaginator;
use Tusimo\Resource\Repository\Traits\ProxyAble;
use Tusimo\Resource\Contract\RepositoryProxyAble;

class ProxyRepository extends Repository implements RepositoryProxyAble
{
    use ProxyAble;

    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        return $this->getRepository()->get($id, $select, $with);
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        return $this->getRepository()->getByIds($ids, $select, $with);
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        return $this->getRepository()->add($resource);
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        return $this->getRepository()->batchAdd($resources);
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
        return $this->getRepository()->delete($id);
    }

    /**
     * Batch delete Resource and return the num that deleted.
     */
    public function deleteByIds(array $ids): int
    {
        return $this->getRepository()->deleteByIds($ids);
    }

    /**
     * Get Resource Paginator.
     *
     * @return LengthAwarePaginator
     */
    public function list(Query $query)
    {
        return $this->getRepository()->list($query);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
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
}
