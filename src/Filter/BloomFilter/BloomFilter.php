<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Filter\BloomFilter;

use Tusimo\Resource\Contract\FilterContract;
use Tusimo\Resource\Filter\Bits\BitHandlerContract;

class BloomFilter implements FilterContract
{
    protected BitHandlerContract $driver;

    protected string $bucket;

    protected $hashFunctions = ['bKDRHash', 'sDBMHash', 'jSHash'];

    protected $hash;

    protected $size = 65535;

    public function __construct(BitHandlerContract $driver, string $bucket = 'filters:bloom:', $size = 65535)
    {
        $this->driver = $driver;
        $this->bucket = $bucket;
        $this->hash = new Hash();
        $this->size = $size;
    }

    public function addItem($item)
    {
        $hashes = $this->calculateHash($item . '');
        $this->getDriver()->setBits($this->getBucket(), $hashes);
    }

    public function addItems(array $items)
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function exists($item): bool
    {
        $hashes = $this->calculateHash($item . '');
        $result = $this->getDriver()->getBits($this->getBucket(), array_keys($hashes));
        foreach ($result as $hashValue) {
            if ($hashValue == 0) {
                return false;
            }
        }
        return true;
    }

    public function notExists($item): bool
    {
        $hashes = $this->calculateHash($item . '');
        $result = $this->getDriver()->getBits($this->getBucket(), array_keys($hashes));
        foreach ($result as $hashValue) {
            if ($hashValue == 1) {
                return false;
            }
        }
        return true;
    }

    public function removeItem($item)
    {
    }

    public function removeItems(array $items)
    {
    }

    /**
     * Get the value of hashFunctions.
     */
    public function getHashFunctions()
    {
        return $this->hashFunctions;
    }

    /**
     * Set the value of hashFunctions.
     *
     * @param mixed $hashFunctions
     * @return self
     */
    public function setHashFunctions($hashFunctions)
    {
        $this->hashFunctions = $hashFunctions;

        return $this;
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

    protected function calculateHash($item, $value = 1): array
    {
        $result = [];
        foreach ($this->hashFunctions as $func) {
            $hashKey = $this->hash->{$func}($item);
            $hashKey = $hashKey % $this->size;
            $result[$hashKey] = $value;
        }
        return $result;
    }
}
