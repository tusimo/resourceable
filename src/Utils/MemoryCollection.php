<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

use Hyperf\Utils\Arr;
use Hyperf\Utils\Collection;
use Hyperf\Paginator\LengthAwarePaginator;

class MemoryCollection extends Collection
{
    /**
     * Select column from collection only with given keys.
     *
     * @return static
     */
    public function select(array $selects = []): self
    {
        $items = $this->all();
        if ($selects && ! in_array('*', $selects)) {
            $newItems = [];
            foreach ($items as $value) {
                $newItems[] = Arr::only($value, $selects);
            }

            return new static($newItems);
        }
        return new static($items);
    }

    /**
     * Order By.
     */
    public function orderBy(string $orderBy, string $order = 'desc'): self
    {
        return $this->sortBy($orderBy, SORT_REGULAR, $order === 'desc');
    }

    /**
     * Get Collection Skip the given number.
     */
    public function offset(int $offset): self
    {
        return $this->slice($offset);
    }

    /**
     * Get the given number items.
     */
    public function limit(int $limit): self
    {
        return $this->take($limit);
    }

    /**
     * Filter the collection between values.
     */
    public function whereBetween(string $key, array $values): self
    {
        return $this->filter(function ($value) use ($key, $values) {
            $target = $value[$key] ?? 0;
            return $target >= $values[0]
                && $target <= $values[1];
        });
    }

    /**
     * Filter the collection not between the values.
     */
    public function whereNotBetween(string $key, array $values): self
    {
        return $this->filter(function ($value) use ($key, $values) {
            $target = $value[$key] ?? 0;
            return $target < $values[0]
                || $target > $values[1];
        });
    }

    /**
     * Filter the collection with the begin given value.
     *
     * @param string $value
     */
    public function whereLike(string $key, $value): self
    {
        return $this->filter(function ($item) use ($key, $value) {
            $target = $item[$key] ?? '';
            return strpos($target, $value) === 0;
        });
    }

    /**
     * Filter the collection where the key value is null.
     */
    public function whereNull(string $key): self
    {
        return $this->filter(function ($value) use ($key) {
            $target = $value[$key] ?? null;
            return is_null($target);
        });
    }

    /**
     * Filter the collection where the key value is not null.
     */
    public function whereNotNull(string $key): self
    {
        return $this->filter(function ($value) use ($key) {
            $target = $value[$key] ?? null;
            return is_null($target);
        });
    }

    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): LengthAwarePaginator
    {
        $total = $this->count();
        $collection = $this->forPage($page, $perPage);
        $collection = $collection->select($columns);
        return new LengthAwarePaginator($collection->toArray(), $total, $perPage, $page);
    }

    /**
     * Delete values in current collection.
     *
     * @param mixed $value
     */
    public function deleteBy(string $key, $value): self
    {
        $newItems = [];
        foreach ($this->items as $item) {
            if (($item[$key] ?? '') != $value) {
                $newItems[] = $item;
            }
        }
        $this->items = $newItems;
        return $this;
    }

    public function updateBy(string $key, $value, array $values): self
    {
        foreach ($this->items as $key => $item) {
            if (($item[$key] ?? '') == $value) {
                $this->items[$key] = array_merge($this->items[$key], $values);
            }
        }
        return $this;
    }

    /**
     * Count by the given keys.
     */
    public function countByKey(string $key): int
    {
        if ($key == '*' || $key == 1) {
            return $this->count();
        }
        return $this->whereNotNull($key)->count();
    }

    /**
     * Truncate all items in the collection.
     */
    public function truncate(): self
    {
        $this->items = [];
        return $this;
    }
}
