<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Contract;

use Tusimo\Query\Query;
use Tusimo\Resource\Entity\Resource;
use Hyperf\Paginator\LengthAwarePaginator;

interface ResourceRepositoryContract
{
    /**
     * Get Resource By Resource Id.
     *
     * @param int|string $id
     *
     * @throws \RuntimeException
     */
    public function get($id, array $select = [], array $with = []): array;

    /**
     * Get Resources by Resource Ids.
     *
     * @throws \RuntimeException
     */
    public function getByIds(array $ids, array $select = [], array $with = []): array;

    /**
     * Add resource.
     *
     * @throws \RuntimeException
     */
    public function add(array $resource): array;

    /**
     * Batch add resources.
     *
     * @throws \RuntimeException
     */
    public function batchAdd(array $resources): array;

    /**
     * Update Resource.
     *
     * @param int|string $id
     * @throws \RuntimeException
     */
    public function update($id, array $resource): array;

    /**
     * Batch update resources.
     *
     * @throws \RuntimeException
     */
    public function batchUpdate(array $resources): array;

    /**
     * Delete resource.
     *
     * @param int|string $id
     * @throws \RuntimeException
     */
    public function delete($id): bool;

    /**
     * Delete resources by resource ids.
     *
     * @throws \RuntimeException
     */
    public function deleteByIds(array $ids): int;

    /**
     * Get Resource Paginator.
     *
     * @return LengthAwarePaginator
     * @throws \RuntimeException
     */
    public function list(Query $query);

    /**
     * Get Resource By Query.
     *
     * @throws \RuntimeException
     */
    public function getByQuery(Query $query): array;

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
     * @throws \RuntimeException
     */
    public function aggregate(Query $query): array;
}
