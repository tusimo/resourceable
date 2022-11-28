<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Relations;

use Hyperf\Utils\Context;
use Hyperf\Engine\Extension;

class Constraint
{
    protected static $constraint = true;

    /**
     * Indicates if the relation is adding constraints.
     */
    public static function isConstraint(): bool
    {
        if (Extension::isLoaded()) {
            return (bool) Context::get(static::class . '::isConstraint', true);
        }

        return static::$constraint;
    }

    public static function setConstraint(bool $constraint): bool
    {
        if (Extension::isLoaded()) {
            return Context::set(static::class . '::isConstraint', $constraint);
        }

        return static::$constraint = $constraint;
    }
}
