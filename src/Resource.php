<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource;

use Tusimo\Resource\Model\Model;
use Tusimo\Resource\Model\Events\Booted;
use Tusimo\Resource\Model\Scopes\ContextAppScope;

abstract class Resource extends Model
{
    protected $contextAppScope = true;

    public function booted(Booted $event)
    {
        if ($this->contextAppScope) {
            static::addGlobalScope(new ContextAppScope());
        }
    }
}
