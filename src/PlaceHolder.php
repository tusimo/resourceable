<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource;

use Tusimo\Resource\Repository\NullRepository;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

/**
 * PlaceHolder Resource
 * only for specific purpose
 * use null repository.
 */
abstract class PlaceHolder extends Resource
{
    protected string $resourceName = 'place_holders';

    /**
     * Get resource Repository using.
     */
    final public function repository(): ResourceRepositoryContract
    {
        return new NullRepository();
    }
}
