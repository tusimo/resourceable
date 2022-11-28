<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Events;

use Tusimo\Resource\Model\Model;

class RemoteEvent extends CustomEvent
{
    public function __construct(string $name, Model $model, array $extra = [])
    {
        $name = 'remote.' . $name;
        parent::__construct($name, $model, $extra);
    }
}
