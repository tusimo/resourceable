<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

class MemoryCollectionManager
{
    /**
     * Shared Store Collections.
     *
     * @var array
     */
    private static $store = [];

    public static function getCollection(string $resourceName): MemoryCollection
    {
        if (! isset(self::$store[$resourceName])) {
            self::$store[$resourceName] = new MemoryCollection();
        }
        return self::$store[$resourceName];
    }
}
