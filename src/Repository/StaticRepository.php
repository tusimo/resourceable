<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Resource\Resource;
use Tusimo\Resource\Utils\MemoryCollection;
use Tusimo\Resource\Repository\Traits\CollectionRepositoryQueryAble;

class StaticRepository extends Repository
{
    use CollectionRepositoryQueryAble;

    protected array $data = [];

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function getCollection(): MemoryCollection
    {
        return new MemoryCollection($this->getData());
    }

    /**
     * Add Resource and return Resource With Id.
     */
    public function add(array $resource): array
    {
        throw new \RuntimeException('static repository can not add');
    }

    /**
     * Batch add resource.
     */
    public function batchAdd(array $resources): array
    {
        throw new \RuntimeException('static repository can not batch add');
    }

    /**
     * Update Resource.
     *
     * @param int|string $id
     */
    public function update($id, array $resource): array
    {
        throw new \RuntimeException('static repository can not update');
    }

    /**
     * Batch Update Resource.
     */
    public function batchUpdate(array $resources): array
    {
        throw new \RuntimeException('static repository can not batch update');
    }

    /**
     * Delete resource.
     *
     * @param int|string $id
     */
    public function delete($id): bool
    {
        throw new \RuntimeException('static repository can not delete');
    }

    /**
     * Batch delete Resource.
     */
    public function deleteByIds(array $ids): int
    {
        throw new \RuntimeException('static repository can not batch delete');
    }
}
