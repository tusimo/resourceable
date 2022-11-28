<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Resolver;

use Tusimo\Resource\Component\GuzzleHttpClientPool;
use Tusimo\Resource\Contract\ClientResolverContract;

class PoolClientResolver implements ClientResolverContract
{
    public function getClient(): \GuzzleHttp\Client
    {
        return GuzzleHttpClientPool::createDefaultPool()->getClient();
    }
}
