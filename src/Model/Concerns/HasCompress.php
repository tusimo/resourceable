<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

use Tusimo\Resource\Model\ModelMeta;
use Hyperf\Contract\UnCompressInterface;
use Tusimo\Resource\Model\ModelStatusMeta;

trait HasCompress
{
    protected $compressWithStatus = false;

    public function compressWithStatus()
    {
        $this->compressWithStatus = true;
        return $this;
    }

    public function isCompressWithStatus()
    {
        return $this->compressWithStatus;
    }

    public function compressWithoutStatus()
    {
        $this->compressWithStatus = false;
        return $this;
    }

    public function compress(): UnCompressInterface
    {
        if ($this->isCompressWithStatus()) {
            return new ModelStatusMeta($this);
        }
        $key = $this->getKey();
        $class = get_class($this);

        return new ModelMeta($class, $key);
    }
}
