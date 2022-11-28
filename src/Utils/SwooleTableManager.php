<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

class SwooleTableManager
{
    /**
     * Shared Swoole Tables.
     *
     * @var array
     */
    private static $tables = [];

    public static function getTable(string $resourceName, int $length = 1024, int $dataSize = 4096): Table
    {
        if (! isset(self::$tables[$resourceName])) {
            $table = new Table($length, 1);
            $table->setDataSize($dataSize);
            $table->column('data', \Swoole\Table::TYPE_STRING, $table->getDataSize());
            $table->column('expired_at', \Swoole\Table::TYPE_STRING, 19);
            $table->create();
            self::$tables[$resourceName] = $table;
        }
        return self::$tables[$resourceName];
    }

    public static function initTable(string $resourceName, int $capacity = 1024, int $dataSize = 4096): Table
    {
        return self::getTable($resourceName, $capacity, $dataSize);
    }
}
