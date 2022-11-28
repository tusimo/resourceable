<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Filter\Bits;

interface BitHandlerContract
{
    /**
     * Set the hash bit value into the specified bucket.
     *
     * @param int $value should be 1 or 0
     */
    public function setBit(string $bucket, int $hash, int $value);

    /**
     * Set the hash bits value into the specified bucket.
     */
    public function setBits(string $bucket, array $hashValues);

    /**
     * Get the value in the specified bucket.
     */
    public function getBit(string $bucket, int $hash): int;

    /**
     * Get the value in the specified bucket.
     */
    public function getBits(string $bucket, array $hashArray): array;

    /**
     * Empty the bucket.
     */
    public function empty(string $bucket);

    /**
     * Check the bucket is empty or not.
     */
    public function isEmpty(string $bucket): bool;
}
