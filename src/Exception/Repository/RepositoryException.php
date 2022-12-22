<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Exception\Repository;

use Tusimo\Resource\Exception\Service\ServiceException;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class RepositoryException extends ServiceException
{
    /**
     * ResourceRepository.
     *
     * @var ResourceRepositoryContract
     */
    protected $repository;

    public function __construct(
        $repository,
        string $message = '',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 500, $previous, 500);
        $this->repository = $repository;
    }

    /**
     * Get resourceRepository.
     *
     * @return ResourceRepositoryContract
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Set resourceRepository.
     *
     * @param ResourceRepositoryContract $repository resourceRepository
     *
     * @return self
     */
    public function setRepository(ResourceRepositoryContract $repository)
    {
        $this->repository = $repository;

        return $this;
    }
}
