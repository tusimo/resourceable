<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

class LRUCacheManager
{
    /**
     * Shared Store Collections.
     *
     * @var array
     */
    private static $store = [];

    public static function getLRUCache(string $resourceName, int $capacity = 1024): LRUCache
    {
        if (! isset(self::$store[$resourceName])) {
            self::$store[$resourceName] = new LRUCache($capacity);
        }
        return self::$store[$resourceName];
    }

    public static function initLRUCache(string $resourceName, int $capacity = 1024): LRUCache
    {
        return self::getLRUCache($resourceName, $capacity);
    }
}
