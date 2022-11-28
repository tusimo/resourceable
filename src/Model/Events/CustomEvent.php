<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Events;

use Hyperf\Utils\Str;
use Tusimo\Resource\Model\Model;
use Psr\EventDispatcher\StoppableEventInterface;

class CustomEvent extends Event implements StoppableEventInterface
{
    /**
     * Event name.
     */
    protected string $name;

    /**
     * Event data.
     */
    protected array $extra = [];

    /**
     * Event method.
     * Name should be like user.registered or user.deleted.
     * The method will auto set like userRegistered or userDeleted.
     */
    public function __construct(string $name, Model $model, array $extra = [])
    {
        $this->name = $name;
        $this->extra = $extra;
        parent::__construct($model, $this->generateMethodByName($name));
    }

    /**
     * Get the value of name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name.
     *
     * @param mixed $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function is(string $name): bool
    {
        return $this->name === $name;
    }

    /**
     * Get event data.
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set event data.
     *
     * @param mixed $extra
     * @return self
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get the remote event.
     */
    public function transformToRemote()
    {
        return new RemoteEvent($this->getName(), $this->getModel(), $this->getExtra());
    }

    /**
     * Get the event method.
     */
    public function transformToQueue()
    {
        return new QueueEvent($this->getName(), $this->getModel(), $this->getExtra());
    }

    public static function createFromBase(Event $event)
    {
        $name = Str::snake($event->getMethod());
        $name = str_replace('_', '.', $name);
        return new static($name, $event->getModel(), []);
    }

    protected function generateMethodByName(string $name): string
    {
        // replace all dot in name
        $name = str_replace('.', '_', $name);
        return Str::camel($name);
    }
}
