<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Scopes;

use Tusimo\Resource\Model\Model;
use Tusimo\Resource\Model\Scope;
use Tusimo\Resource\Model\Builder;

class ContextAppScope implements Scope
{
    /**
     * Apply the scope to a given Model query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        if (! empty(request_context()->getApp())) {
            $builder->where('app', request_context()->getApp());
        }
    }
}
