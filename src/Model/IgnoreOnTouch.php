<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model;

class IgnoreOnTouch
{
    /**
     * The list of models classes that should not be affected with touch.
     *
     * @var array
     */
    public static $container = [];
}
