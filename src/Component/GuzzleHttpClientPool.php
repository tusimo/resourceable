<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Component;

use GuzzleHttp\Client;

class GuzzleHttpClientPool
{
    private $stack;

    private int $minConnections;

    private int $maxConnections;

    private float $waitTimeout;

    private float $maxIdleTimeout;

    public function __construct(
        $minConnections = 10,
        $maxConnections = 500,
        $waitTimeout = 3.0,
        $maxIdleTimeout = 60.0
    ) {
        $this->minConnections = $minConnections;
        $this->maxConnections = $maxConnections;
        $this->waitTimeout = $waitTimeout;
        $this->maxIdleTimeout = $maxIdleTimeout;
        $this->setStack();
    }

    public function getClient(): Client
    {
        return make(Client::class, [
            'config' => [
                'handler' => $this->stack,
            ],
        ]);
    }

    public static function createDefaultPool(): self
    {
        return new self();
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function setStack()
    {
        $this->stack = container()->get(\Hyperf\Guzzle\HandlerStackFactory::class)->create([
            'min_connections' => $this->minConnections,
            'max_connections' => $this->maxConnections,
            'wait_timeout' => $this->waitTimeout,
            'max_idle_time' => $this->maxIdleTimeout,
        ]);
        return $this;
    }
}
