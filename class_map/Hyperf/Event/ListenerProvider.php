<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Hyperf\Event;

use Psr\EventDispatcher\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var ListenerData[]
     */
    public $listeners = [];

    /**
     * @param object $event An event for which to return the relevant listeners
     * @return iterable[callable] An iterable (array, iterator, or generator) of callables.  Each
     *                            callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent($event): iterable
    {
        $queue = new \SplPriorityQueue();
        foreach ($this->listeners as $listener) {
            if ($event instanceof $listener->event
                || (method_exists($event, 'getName') && $event->getName() == $listener->event)
            ) {
                $queue->insert($listener->listener, $listener->priority);
            }
        }
        return $queue;
    }

    public function on(string $event, callable $listener, int $priority = 1): void
    {
        $this->listeners[] = new ListenerData($event, $listener, $priority);
    }
}
