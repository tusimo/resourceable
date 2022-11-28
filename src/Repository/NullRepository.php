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

class NullRepository extends Repository
{
    /**
     * Get Resource by id.
     *
     * @param int|string $id
     */
    public function get($id, array $select = [], array $with = []): array
    {
        return [];
    }

    /**
     * Get Resources by Ids.
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array
    {
        return [];
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        return [];
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        return [];
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        return [];
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        return [];
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        return true;
    }

    /**
     * Batch delete Resource and return the num that deleted.
     */
    public function deleteByIds(array $ids): int
    {
        return 0;
    }

    /**
     * Get Resource Paginator.
     *
     * @return LengthAwarePaginator
     */
    public function list(Query $query)
    {
        return new LengthAwarePaginator([], 0, 0, 0);
    }

    /**
     * Get Resource By Query.
     */
    public function getByQuery(Query $query): array
    {
        return [];
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
        return [];
    }
}
