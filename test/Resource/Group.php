<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Resource;

use Tusimo\Resource\Model\Model;
use Tusimo\Resource\Repository\ApiRepository;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class Group extends Model
{
    protected string $resourceName = 'groups';

    protected $attributes = [
        'created_by' => '1000',
    ];

    /**
     * Get resource Repository using.
     */
    public function repository(): ResourceRepositoryContract
    {
        return new ApiRepository(
            'http://translation.rd-development.svc.cluster.local',
            'groups',
            'v1'
        );
    }
}
