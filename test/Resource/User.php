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

class User extends Resource
{
    protected string $resourceName = 'users';

    protected $attributes = [
    ];

    protected $rules = [
        'name' => 'string|max:3',
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

    public function roles()
    {
        return $this->belongsToMany(Role::class, UserRole::class);
    }

    // @codingStandardsIgnoreStart
    public function user_roles()
    {
        return $this->hasMany(UserRole::class, 'user_id', 'id');
    }
    // @codingStandardsIgnoreEnd
}
