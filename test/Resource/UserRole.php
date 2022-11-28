<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Test\Resource;

use Tusimo\Resource\Model\Relations\Pivot;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class UserRole extends Pivot
{
    protected string $resourceName = 'user_role';

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
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'id', 'role_id');
    }
}
