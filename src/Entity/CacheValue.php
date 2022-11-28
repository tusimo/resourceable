<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Entity;

class CacheValue
{
    protected bool $exists = true;

    protected $value;

    protected $resourceKeyName;

    public function __construct($value, bool $exists = true, $resourceKeyName = 'id')
    {
        $this->value = $value;
        $this->exists = $exists;
        $this->resourceKeyName = $resourceKeyName;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isExists()
    {
        return $this->exists;
    }

    /**
     * Get the Resource Key from the cache value
     * if resource is not exists, false will be returned.
     *
     * @return mixed
     */
    public function getResourceKey()
    {
        if ($this->isExists()) {
            return $this->value[$this->getResourceKeyName()] ?? false;
        }
        return false;
    }

    /**
     * Get the value of resourceKeyName.
     */
    public function getResourceKeyName()
    {
        return $this->resourceKeyName;
    }

    /**
     * Set the value of resourceKeyName.
     *
     * @param mixed $resourceKeyName
     * @return self
     */
    public function setResourceKeyName($resourceKeyName)
    {
        $this->resourceKeyName = $resourceKeyName;

        return $this;
    }
}
