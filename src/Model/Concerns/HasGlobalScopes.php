<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

use Hyperf\Utils\Arr;
use Tusimo\Resource\Model\Scope;
use Tusimo\Resource\Model\GlobalScope;

trait HasGlobalScopes
{
    /**
     * Register a new global scope on the model.
     *
     * @param \Closure|string|\Tusimo\Resource\Model\Scope $scope
     *
     * @throws \InvalidArgumentException
     */
    public static function addGlobalScope($scope, \Closure $implementation = null)
    {
        if (is_string($scope) && ! is_null($implementation)) {
            return GlobalScope::$container[static::class][$scope] = $implementation;
        }
        if ($scope instanceof \Closure) {
            return GlobalScope::$container[static::class][spl_object_hash($scope)] = $scope;
        }
        if ($scope instanceof Scope) {
            return GlobalScope::$container[static::class][get_class($scope)] = $scope;
        }

        throw new \InvalidArgumentException('Global scope must be an instance of Closure or Scope.');
    }

    /**
     * Determine if a model has a global scope.
     *
     * @param string|\Tusimo\Resource\Model\Scope $scope
     * @return bool
     */
    public static function hasGlobalScope($scope)
    {
        return ! is_null(static::getGlobalScope($scope));
    }

    /**
     * Get a global scope registered with the model.
     *
     * @param string|\Tusimo\Resource\Model\Scope $scope
     * @return null|\Closure|\Tusimo\Resource\Model\Scope
     */
    public static function getGlobalScope($scope)
    {
        if (is_string($scope)) {
            return Arr::get(GlobalScope::$container, static::class . '.' . $scope);
        }

        return Arr::get(
            GlobalScope::$container,
            static::class . '.' . get_class($scope)
        );
    }

    /**
     * Get the global scopes for this class instance.
     *
     * @return array
     */
    public function getGlobalScopes()
    {
        return Arr::get(GlobalScope::$container, static::class, []);
    }
}
