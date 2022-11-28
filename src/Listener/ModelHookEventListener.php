<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Listener;

use Tusimo\Resource\Model\Events\Event;
use Tusimo\Resource\Job\Base\QueueTrait;
use Hyperf\Event\Contract\ListenerInterface;
use Tusimo\Resource\Job\Base\ResourceEventJob;

class ModelHookEventListener implements ListenerInterface
{
    use QueueTrait;

    public function listen(): array
    {
        return [
            Event::class,
        ];
    }

    /**
     * @param Event $event
     */
    public function process(object $event)
    {
        $event->handle();
        if ($event->getModel()->shouldQueueEvent($event)) {
            $queueEvent = $event->transformToQueue();
            $this->queue(new ResourceEventJob($queueEvent));
        }
    }
}
