<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Relations;

use Tusimo\Resource\Resource;
use Tusimo\Resource\Model\Relations\Concerns\AsPivot;

abstract class Pivot extends Resource
{
    use AsPivot;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
