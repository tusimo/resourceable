<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

use Tusimo\Resource\Model\Events\Saved;
use Tusimo\Resource\Model\Events\Booted;
use Tusimo\Resource\Model\Events\Saving;
use Tusimo\Resource\Model\Events\Booting;
use Tusimo\Resource\Model\Events\Created;
use Tusimo\Resource\Model\Events\Deleted;
use Tusimo\Resource\Model\Events\Updated;
use Tusimo\Resource\Model\Events\Creating;
use Tusimo\Resource\Model\Events\Deleting;
use Tusimo\Resource\Model\Events\Restored;
use Tusimo\Resource\Model\Events\Updating;
use Tusimo\Resource\Model\Events\Restoring;
use Tusimo\Resource\Model\Events\Retrieved;
use Tusimo\Resource\Model\Events\CustomEvent;
use Tusimo\Resource\Model\Events\ForceDeleted;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @method retrieved(Retrieved $event)
 * @method creating(Creating $event)
 * @method created(Created $event)
 * @method updating(Updating $event)
 * @method updated(Updated $event)
 * @method saving(Saving $event)
 * @method saved(Saved $event)
 * @method restoring(Restoring $event)
 * @method restored(Restored $event)
 * @method deleting(Deleting $event)
 * @method deleted(Deleted $event)
 * @method forceDeleted(ForceDeleted $event)
 */
trait HasEvents
{
    /**
     * User exposed events.
     *
     * @var array
     */
    protected $events = [];

    protected bool $withoutEvents = false;

    public function withEvents()
    {
        $this->withoutEvents = false;
        return $this;
    }

    public function withoutEvents()
    {
        $this->withoutEvents = true;
        return $this;
    }

    public function shouldDispatchEvents(): bool
    {
        return ! $this->withoutEvents;
    }

    /**
     * Set the user-defined event names.
     */
    public function setEvents(array $events): self
    {
        foreach ($events as $key => $value) {
            if (is_numeric($key) && is_string($value)) {
                $events[$value] = '';
                unset($events[$key]);
            }
        }

        $this->events = $events;

        return $this;
    }

    /**
     * Add some observable event.
     *
     * @param array|string $events
     */
    public function addEvents($events): void
    {
        $this->events = array_unique(array_merge($this->events, is_array($events) ? $events : func_get_args()));
    }

    /**
     * Remove some registered event.
     */
    public function removeEvents(array $events): void
    {
        foreach ($events as $value) {
            if (isset($this->events[$value])) {
                // When passing the key of event.
                unset($this->events[$value]);
            } elseif (class_exists($value) && $key = array_search($value, $this->events)) {
                // When passing the class of event.
                unset($this->events[$key]);
            }
        }
    }

    /**
     * Get the available event names, the custom event will override the default event.
     *
     * @return array [EventMethodName => EventClass]
     */
    public function getAvailableEvents(): array
    {
        return array_replace($this->getDefaultEvents(), $this->events);
    }

    public function fire(string $event)
    {
        return $this->shouldDispatchEvents() && $this->getEventDispatcher()->dispatch(new CustomEvent($event, $this));
    }

    /**
     * Get the default events of Hyperf Database Model.
     */
    protected function getDefaultEvents(): array
    {
        return [
            'booting' => Booting::class,
            'booted' => Booted::class,
            'retrieved' => Retrieved::class,
            'creating' => Creating::class,
            'created' => Created::class,
            'updating' => Updating::class,
            'updated' => Updated::class,
            'saving' => Saving::class,
            'saved' => Saved::class,
            'restoring' => Restoring::class,
            'restored' => Restored::class,
            'deleting' => Deleting::class,
            'deleted' => Deleted::class,
            'forceDeleted' => ForceDeleted::class,
        ];
    }

    /**
     * Fire the given event for the model.
     *
     * @return null|object|StoppableEventInterface
     */
    protected function fireModelEvent(string $event): ?object
    {
        if (! $this->shouldDispatchEvents()) {
            return null;
        }

        $dispatcher = $this->getEventDispatcher();
        if (! $dispatcher instanceof EventDispatcherInterface) {
            return null;
        }

        $result = $this->fireCustomModelEvent($event);
        // If custom event does not exist, the fireCustomModelEvent() method will return null.
        if (! is_null($result)) {
            return $result;
        }

        // If the model is not running in Hyperf, then the listener method of model will not bind to the EventDispatcher automatically.
        $eventName = $this->getDefaultEvents()[$event];
        return $dispatcher->dispatch(new $eventName($this, $event));
    }

    /**
     * Fire a custom model event for the given event.
     *
     * @return null|object|StoppableEventInterface
     */
    protected function fireCustomModelEvent(string $event)
    {
        if (! isset($this->events[$event])) {
            return;
        }

        return $this->getEventDispatcher()->dispatch(new $this->events[$event]($this));
    }
}
