<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Kafka\Base;

use Tusimo\Resource\Entity\RequestContext;

class Event
{
    protected RequestContext $requestContext;

    protected string $name;

    protected $data;

    protected array $meta;

    protected string $key;

    /**
     * Get the value of requestContext.
     */
    public function getRequestContext()
    {
        return $this->requestContext;
    }

    /**
     * Set the value of requestContext.
     *
     * @param mixed $requestContext
     * @return self
     */
    public function setRequestContext($requestContext)
    {
        $this->requestContext = $requestContext;

        return $this;
    }

    /**
     * Get the value of name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name.
     *
     * @param mixed $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of data.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data.
     *
     * @param mixed $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the value of meta.
     * @param null|mixed $meta
     */
    public function getMeta($meta = null)
    {
        if ($meta) {
            return $this->meta[$meta] ?? null;
        }
        return $this->meta;
    }

    /**
     * Set the value of meta.
     *
     * @param mixed $meta
     * @return self
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get the value of key.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the value of key.
     *
     * @param mixed $key
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }
}
