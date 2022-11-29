<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Tusimo\Resource\Contract\ResourceRepositoryContract;

abstract class Repository implements ResourceRepositoryContract
{
    /**
     * Resource name.
     */
    protected string $resourceName;

    /**
     * Resource KeyName.
     */
    protected string $keyName = 'id';

    /**
     * Get resource name.
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Set resource name.
     *
     * @param string $resourceName Resource name
     *
     * @return self
     */
    public function setResourceName(string $resourceName)
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    /**
     * Get resource KeyName.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * Set resource KeyName.
     *
     * @param string $keyName Resource KeyName
     *
     * @return static
     */
    public function setKeyName(string $keyName)
    {
        $this->keyName = $keyName;

        return $this;
    }

    protected function shouldSelect(array $select = []): bool
    {
        return $select && ! in_array('*', $select);
    }

    protected function getParsedSelect(array $select = []): array
    {
        if ($this->shouldSelect($select)) {
            return array_merge($select, [$this->getKeyName()]);
        }
        return [];
    }
}
