<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

use Hyperf\Utils\Str;
use Tusimo\Resource\Model\Events\Event;

trait HasQueueEvent
{
    /**
     * Undocumented function.
     *
     * @return bool
     */
    public function shouldQueueEvent(Event $event)
    {
        if ($this->hasQueueMethod($event)) {
            return true;
        }
        return false;
    }

    public function getQueueEventMethodName(Event $event): string
    {
        return Str::camel('queue' . $event->getMethod());
    }

    public function hasQueueMethod(Event $event)
    {
        if (method_exists($this, $this->getQueueEventMethodName($event))) {
            return true;
        }
        return false;
    }
}
