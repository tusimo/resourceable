<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Resource;

use Tusimo\Resource\Resource;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class Role extends Resource
{
    protected string $resourceName = 'roles';

    protected $attributes = [
    ];

    /**
     * Get resource Repository using.
     */
    public function repository(): ResourceRepositoryContract
    {
        return $this->collectionRepository();
    }

    public function tokens()
    {
        return $this->hasMany(Token::class, 'id', 'token_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, UserRole::class);
    }

    // @codingStandardsIgnoreStart
    public function user_roles()
    {
        return $this->hasMany(UserRole::class, 'role_id', 'id');
    }
    // @codingStandardsIgnoreEnd
}
