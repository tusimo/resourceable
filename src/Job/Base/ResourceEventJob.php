<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Job\Base;

use Tusimo\Resource\Model\Events\Event;

class ResourceEventJob extends BaseJob
{
    /**
     * Undocumented variable.
     *
     * @var Event
     */
    public $modelEvent;

    public function __construct(Event $modelEvent)
    {
        $this->modelEvent = $modelEvent;
    }

    public function handle()
    {
        $this->modelEvent->handle();
    }
}
