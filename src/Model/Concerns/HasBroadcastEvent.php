<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

use Tusimo\Resource\Model\Events\Event;
use Tusimo\Resource\Model\Events\CustomEvent;

trait HasBroadcastEvent
{
    protected $shouldBroadcast = true;

    protected $broadcastEvents = [
        // 'created',
        // 'updated',
        // 'saved',
        // 'restored',
        // 'deleted',
        // 'forceDeleted',
    ];

    /**
     * Undocumented function.
     *
     * @return static
     */
    public function withoutBroadcastEvents(array $events)
    {
        $this->broadcastEvents = array_diff($this->broadcastEvents, $events);
        return $this;
    }

    /**
     * Undocumented function.
     *
     * @return static
     */
    public function withBroadcastEvents(array $events)
    {
        $this->broadcastEvents = array_merge($this->broadcastEvents, $events);
        return $this;
    }

    public function hasBroadcastEvent(string $event): bool
    {
        return in_array($event, $this->broadcastEvents);
    }

    /**
     * Don't broadcast.
     *
     * @return static
     */
    public function disableBroadcast()
    {
        $this->shouldBroadcast = false;
        return $this;
    }

    /**
     * Broadcast the event.
     *
     * @return static
     */
    public function enableBroadcast()
    {
        $this->shouldBroadcast = true;
        return $this;
    }

    /**
     * Undocumented function.
     *
     * @return bool
     */
    public function shouldBroadcastEvent(Event $event)
    {
        if (! $this->shouldBroadcast) {
            return false;
        }
        if ($event instanceof CustomEvent) {
            if (! $this->hasBroadcastEvent($event->getName())) {
                return false;
            }
            return true;
        }
        return $this->hasBroadcastEvent($event->getMethod());
    }
}
