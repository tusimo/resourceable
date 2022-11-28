<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Contract;

use GuzzleHttp\Client;

interface ClientResolverContract extends ShouldMockContract
{
    public function getClient(): Client;
}
