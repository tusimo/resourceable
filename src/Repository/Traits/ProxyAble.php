<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository\Traits;

use Tusimo\Resource\Contract\ResourceRepositoryContract;

trait ProxyAble
{
    /**
     * Get Real Repo.
     *
     * @var ResourceRepositoryContract
     */
    protected $repository;

    public function __call($method, $args)
    {
        return $this->getRepository()->{$method}(...$args);
    }

    /**
     * Get get Real Repo.
     */
    public function getRepository(): ResourceRepositoryContract
    {
        return $this->repository;
    }

    /**
     * Set get Real Repo.
     *
     * @param ResourceRepositoryContract $repository Get Real Repo
     *
     * @return self
     */
    public function setRepository(ResourceRepositoryContract $repository)
    {
        $this->repository = $repository;

        return $this;
    }
}
