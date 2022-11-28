<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Job\Base;

use Psr\Container\ContainerInterface;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class QueueService
{
    /**
     * @var DriverFactory
     */
    protected $driverFactory;

    public function __construct(ContainerInterface $container)
    {
        $this->driverFactory = $container->get(DriverFactory::class);
    }

    public function push(BaseJob $job, string $queue = 'default', int $delay = 0): bool
    {
        $job->requestContext = request_context();
        return $this->driverFactory->get($queue)->push($job, $delay);
    }
}
