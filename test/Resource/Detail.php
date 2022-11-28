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

class Detail extends Model
{
    protected string $resourceName = 'details';

    /**
     * Get resource Repository using.
     */
    public function repository(): ResourceRepositoryContract
    {
        return new ApiRepository(
            'http://127.0.0.1',
            'details',
            'v2'
        );
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'token_id', 'token_id');
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id');
    }
}
