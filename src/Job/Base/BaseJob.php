<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Job\Base;

use Hyperf\AsyncQueue\Job;
use Tusimo\Resource\Entity\RequestContext;

abstract class BaseJob extends Job
{
    public RequestContext $requestContext;

    /**
     * Job ID.
     * This property will avoid the same property job overwrite each other.
     */
    protected string $jobId;

    public function __construct()
    {
        $this->jobId = uniqid();
    }

    public function handle()
    {
    }
}
