<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Contract;

interface FilterContract
{
    /**
     * Add Item to filter.
     *
     * @param int|string $item
     */
    public function addItem($item);

    /**
     * Add Items to filter.
     */
    public function addItems(array $items);

    /**
     * Check if Item exists in filter.
     *
     * @param int|string $item
     */
    public function exists($item): bool;

    /**
     * Check if Item not exists in filter.
     *
     * @param int|string $item
     */
    public function notExists($item): bool;

    /**
     * Remove Item from filter.
     *
     * @param int|string $item
     */
    public function removeItem($item);

    /**
     * Remove items from filter.
     */
    public function removeItems(array $items);

    /**
     * Get the bucket name.
     */
    public function getBucket(): string;

    /**
     * Set the bucket.
     */
    public function setBucket(string $bucket);

    /**
     * Empty the bucket.
     */
    public function empty();

    /**
     * Check the bucket is empty or not.
     */
    public function isEmpty(): bool;
}
