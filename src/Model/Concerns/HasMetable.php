<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

trait HasMetable
{
    public function getMeta(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->meta;
        }
        return data_get($this->meta, $key, $default);
    }

    public function setMeta(string $key, $value)
    {
        data_set($this->meta, $key, $value);
        return $this;
    }
}
