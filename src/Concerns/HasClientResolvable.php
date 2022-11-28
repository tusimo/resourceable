<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Concerns;

use GuzzleHttp\Client;
use Tusimo\Resource\Resolver\DefaultClientResolver;
use Tusimo\Resource\Contract\ClientResolverContract;

trait HasClientResolvable
{
    /**
     * Seconds before connect to the client.
     */
    protected float $connectTimeout = 2.0;

    /**
     * Seconds before request send back.
     */
    protected float $timeout = 6.0;

    /**
     * Debug Modal.
     */
    protected bool $debug = false;

    protected ?ClientResolverContract $clientResolver;

    /**
     * Set ClientResolver.
     *
     * @return static
     */
    public function setClientResolver(?ClientResolverContract $clientResolver = null)
    {
        $this->clientResolver = $clientResolver;
        return $this;
    }

    /**
     * Get a client.
     */
    public function getClient(): Client
    {
        return $this->clientResolver->getClient();
    }

    /**
     * Get the value of clientResolver.
     */
    public function getClientResolver()
    {
        if (is_null($this->clientResolver)) {
            $clientResolver = new DefaultClientResolver();
        }
        $this->clientResolver = $clientResolver;
        return $this->clientResolver;
    }

    /**
     * Get the value of connectTimeout.
     */
    public function getConnectTimeout()
    {
        if ($this->getDebug()) {
            return 0;
        }
        return $this->connectTimeout;
    }

    /**
     * Set the value of connectTimeout.
     *
     * @param mixed $connectTimeout
     * @return self
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = $connectTimeout;

        return $this;
    }

    /**
     * Get the value of timeout.
     */
    public function getTimeout()
    {
        if ($this->getDebug()) {
            return 0;
        }
        return $this->timeout;
    }

    /**
     * Set the value of timeout.
     *
     * @param mixed $timeout
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the value of debug.
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Set the value of debug.
     *
     * @param mixed $debug
     * @return self
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }
}
