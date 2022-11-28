<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model;

interface Scope
{
    /**
     * Apply the scope to a given Model query builder.
     *
     * @param \Tusimo\Resource\Model\Builder $builder
     * @param \Tusimo\Resource\Model\Model $model
     */
    public function apply(Builder $builder, Model $model);
}
