<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Resolver;

use Tusimo\Resource\Contract\ClientResolverContract;

class DefaultClientResolver implements ClientResolverContract
{
    public function getClient(): \GuzzleHttp\Client
    {
        if (has_container()) {
            return (new PoolClientResolver())->getClient();
        }
        return new \GuzzleHttp\Client();
    }
}
