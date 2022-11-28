<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Utils;

class Table extends \Swoole\Table
{
    protected $dataSize = 4096;

    /**
     * @param mixed $key
     * @return bool
     */
    public function set($key, array $value)
    {
        if ($this->count() >= $this->getSize()) {
            $this->clear();
        }
        $value = ['data' => json_encode($value), 'expired_at' => ''];
        $result = parent::set($key, $value);
        if ($result === false) {
            $this->clear();
            $result = parent::set($key, $value);
        }
        return $result;
    }

    public function setWithExpire($key, ?array $value, $expire)
    {
        if ($this->count() >= $this->getSize()) {
            $this->clear();
        }
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $value = ['data' => $value, 'expired_at' => date('Y-m-d H:i:s', time() + $expire)];
        $result = parent::set($key, $value);
        if ($result === false) {
            $this->clear();
            $result = parent::set($key, $value);
        }
        return $result;
    }

    /**
     * @param mixed $key
     * @param null|mixed $field
     * @return mixed
     */
    public function get($key, $field = null)
    {
        $value = parent::get($key);
        if ($value === false) {
            return false;
        }
        if ($value['expired_at'] !== '' && strtotime($value['expired_at']) < time()) {
            $this->del($key);
            return false;
        }
        if ($field === null) {
            return json_decode($value['data'], true);
        }
        return json_decode($value['data'], true)[$field] ?? null;
    }

    public function exist($key)
    {
        return $this->get($key) !== false;
    }

    /**
     * @param mixed $incrby
     * @param mixed $key
     * @param mixed $column
     * @return int
     */
    public function incr($key, $column, $incrby = 1)
    {
        $value = parent::get($key);
        if ($value === false) {
            return 0;
        }
        $value['data'] = json_decode($value['data'], true);
        $value['data'][$column] += $incrby;
        $value['data'] = json_encode($value['data']);
        parent::set($key, $value);
        return $value['data'][$column];
    }

    /**
     * @param mixed $decrby
     * @param mixed $key
     * @param mixed $column
     * @return int
     */
    public function decr($key, $column, $decrby = 1)
    {
        $value = parent::get($key);
        if ($value === false) {
            return 0;
        }
        $value['data'] = json_decode($value['data'], true);
        $value['data'][$column] -= $decrby;
        $value['data'] = json_encode($value['data']);
        parent::set($key, $value);
        return $value['data'][$column];
    }

    /**
     * Get the value of dataSize.
     */
    public function getDataSize()
    {
        return $this->dataSize;
    }

    /**
     * Set the value of dataSize.
     *
     * @param mixed $dataSize
     * @return self
     */
    public function setDataSize($dataSize)
    {
        $this->dataSize = $dataSize;

        return $this;
    }

    protected function clear()
    {
        foreach ($this as $key => $value) {
            if ($value['expired_at'] !== '' && strtotime($value['expired_at']) < time()) {
                $this->del($key);
                return false;
            }
        }

        if ($this->count() >= $this->getSize()) {
            $total = $this->getSize() * 0.2;
            $index = 0;
            foreach ($this as $key => $value) {
                if ($index >= $total) {
                    break;
                }
                $this->del($key);
                ++$index;
            }
        }
    }
}
