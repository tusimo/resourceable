<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Contract;

interface RepositoryProxyAble
{
    /**
     * Get repository proxy.
     */
    public function getRepository(): ResourceRepositoryContract;

    /**
     * set repository proxy.
     *
     * @return static
     */
    public function setRepository(ResourceRepositoryContract $repository);
}
