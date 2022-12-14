<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model;

use Hyperf\Contract\UnCompressInterface;

class ModelMeta implements UnCompressInterface
{
    /**
     * @var string
     */
    public $class;

    /**
     * @var int|string
     */
    public $key;

    /**
     * @param int|string $key
     */
    public function __construct(string $class, $key)
    {
        $this->class = $class;
        $this->key = $key;
    }

    public function uncompress()
    {
        if (is_null($this->key)) {
            return new $this->class();
        }
        return $this->class::find($this->key);
    }
}
