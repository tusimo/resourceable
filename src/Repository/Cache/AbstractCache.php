<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository\Cache;

use Swoole\Timer;
use Tusimo\Resource\Contract\ResourceCacheAble;

abstract class AbstractCache implements ResourceCacheAble
{
    protected string $keyName = 'id';

    protected string $resourceName;

    protected string $prefix = '';

    public function __construct(string $resourceName, string $keyName = 'id', string $prefix = '')
    {
        $this->keyName = $keyName;
        $this->resourceName = $resourceName;
        $this->prefix = $prefix;
    }

    /**
     * Get the value of keyName.
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * Set the value of keyName.
     *
     * @param mixed $keyName
     * @return self
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * clean resource.
     */
    public function shouldClean(): bool
    {
        return true;
    }

    /**
     * Get the value of resourceName.
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Set the value of resourceName.
     *
     * @param mixed $resourceName
     * @return self
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    /**
     * Get the value of prefix.
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the value of prefix.
     *
     * @param mixed $prefix
     * @return self
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    protected function getCacheKey($id): string
    {
        return $this->getPrefix() . ':' . $this->getResourceName() . ':' . $this->getKeyName() . ':' . $id;
    }

    protected function getCacheKeys(array $keys): array
    {
        return array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);
    }

    protected function getOriginalKey(string $key): string
    {
        return str_replace($this->getCacheKey(''), '', $key);
    }

    protected function setCacheExpiring($id, int $ttl)
    {
        Timer::after($ttl * 1000, function () use ($id) {
            $this->deleteResourceCache($id);
        });
    }

    protected function shouldSelect(array $select = []): bool
    {
        return $select && ! in_array('*', $select);
    }
}
