<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

class IdGenerator
{
    /**
     * Shared Store Collections.
     *
     * @var array
     */
    private static $ids = [];

    private $type = 'int';

    private string $resourceName;

    public function __construct(string $resourceName, $type = 'int')
    {
        $this->resourceName = $resourceName;
        $this->type = $type;
    }

    public function getNextId()
    {
        if ($this->type == 'int') {
            return $this->getIntegerNextId();
        }
        return $this->getStringNextId();
    }

    private function getStringNextId(): string
    {
        return uniqid();
    }

    private function getIntegerNextId(): int
    {
        if (! isset(self::$ids[$this->resourceName])) {
            self::$ids[$this->resourceName] = 1;
        } else {
            ++self::$ids[$this->resourceName];
        }
        return self::$ids[$this->resourceName];
    }
}
