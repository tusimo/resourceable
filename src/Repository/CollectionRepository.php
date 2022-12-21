<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Resource\Utils\IdGenerator;
use Tusimo\Resource\Utils\MemoryCollection;
use Tusimo\Resource\Utils\MemoryCollectionManager;
use Tusimo\Resource\Repository\Traits\CollectionRepositoryQueryAble;

class CollectionRepository extends Repository
{
    use CollectionRepositoryQueryAble;

    protected MemoryCollection $collection;

    protected string $keyType = 'int';

    protected IdGenerator $idGenerator;

    public function __construct(string $resourceName, string $keyName = 'id', string $keyType = 'int', ?MemoryCollection $collection = null)
    {
        $this->keyName = $keyName;
        $this->resourceName = $resourceName;
        $this->keyType = $keyType;
        if (is_null($collection)) {
            $collection = MemoryCollectionManager::getCollection($resourceName);
        }
        $this->setCollection($collection);
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
}
