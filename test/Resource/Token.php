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

class Token extends Model
{
    protected string $resourceName = 'tokens';

    protected $attributes = [
        'created_by' => '1000',
    ];

    /**
     * Get resource Repository using.
     */
    public function repository(): ResourceRepositoryContract
    {
        return new ApiRepository(
            'http://127.0.0.1',
            'tokens',
            'v2'
        );
    }

    public function detail()
    {
        return $this->hasOne(Detail::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function scopeOfApp($query, $app)
    {
        return $query->where('app', $app);
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'app', 'app');
    }
}
