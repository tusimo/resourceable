<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Exception\Resource;

use Tusimo\Resource\Resource;

class ResourceException extends \Exception
{
    /**
     * Resource.
     *
     * @var resource
     */
    protected $resource;

    public function __construct($resource, $message = '', $code = 400, $previous = null)
    {
        $this->resource = $resource;
        parent::__construct(
            $this->resource->getResourceName() . ':' . $this->resource->getKey() . ' ' . $message,
            $code,
            $previous
        );
    }

    /**
     * Get resource.
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set resource.
     *
     * @param resource $resource resource
     *
     * @return self
     */
    public function setResource(Resource $resource)
    {
        $this->resource = $resource;

        return $this;
    }
}
