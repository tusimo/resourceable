<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Filter\BitFilter;

use Tusimo\Resource\Contract\FilterContract;
use Tusimo\Resource\Filter\Bits\BitHandlerContract;

class BitFilter implements FilterContract
{
    protected string $bucket;

    protected BitHandlerContract $driver;

    protected $min = 0;

    protected $max = 10000;

    public function __construct(BitHandlerContract $driver, string $bucket = 'filters:bit', int $min = 0, int $max = 10000)
    {
        $this->driver = $driver;
        $this->bucket = $bucket;
        $this->min = $min;
        $this->max = $max;
        if ($this->min >= $this->max) {
            throw new \RuntimeException('min must be smaller than max');
        }
    }

    public function addItem($item)
    {
        $this->checkItem($item);
        $item = (int) $item;
        $position = $this->getBitPosition($item);
        $this->getDriver()->setBit($this->getBucket(), $position, 1);
    }

    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function exists($item): bool
    {
        $this->checkItem($item);
        $item = (int) $item;
        $position = $this->getBitPosition($item);
        $result = $this->getDriver()->getBit($this->getBucket(), $position);
        return $result === 1;
    }

    public function notExists($item): bool
    {
        return ! $this->exists($item);
    }

    public function removeItem($item)
    {
        $this->checkItem($item);
        $item = (int) $item;
        $position = $this->getBitPosition($item);
        $this->getDriver()->setBit($this->getBucket(), $position, 0);
    }

    public function removeItems(array $items)
    {
        foreach ($items as $item) {
            $this->removeItem($item);
        }
    }

    /**
     * Get the value of bucket.
     */
    public function getBucket(): string
    {
        return $this->bucket;
    }

    /**
     * Set the value of bucket.
     */
    public function setBucket(string $bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * Empty the bucket.
     */
    public function empty()
    {
        return $this->getDriver()->empty($this->getBucket());
    }

    /**
     * Check the bucket is empty or not.
     */
    public function isEmpty(): bool
    {
        return $this->getDriver()->isEmpty($this->getBucket());
    }

    /**
     * Get the value of driver.
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Set the value of driver.
     *
     * @param mixed $driver
     * @return self
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    protected function getBitPosition(int $item): int
    {
        return $item - $this->min;
    }

    protected function checkItem($item)
    {
        if (is_numeric($item)) {
            $item = (int) $item;
        }
        if (! is_integer($item)) {
            throw new \RuntimeException('item must be a integer');
        }
        if ($item < $this->min || $item > $this->max) {
            throw new \RuntimeException('item must be between min and max');
        }
    }
}
