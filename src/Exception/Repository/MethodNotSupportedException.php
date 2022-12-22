<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Exception\Repository;

class MethodNotSupportedException extends RepositoryException
{
    /**
     * Method.
     *
     * @var string
     */
    protected $method;

    public function __construct(
        $repository,
        $method
    ) {
        parent::__construct($repository, "repository method: {$method} not supported");
        $this->method = $method;
    }

    /**
     * Get method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
