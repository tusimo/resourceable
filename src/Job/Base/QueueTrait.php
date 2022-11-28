<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Job\Base;

trait QueueTrait
{
    public function queue(BaseJob $job, string $queue = 'default', int $delay = 0): bool
    {
        $service = make(QueueService::class);
        return $service->push($job, $queue, $delay);
    }
}
