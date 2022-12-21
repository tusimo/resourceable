<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Events;

use Hyperf\Utils\Str;
use Hyperf\Event\Stoppable;
use Tusimo\Resource\Model\Model;
use Tusimo\Resource\Entity\RequestContext;
use Psr\EventDispatcher\StoppableEventInterface;

abstract class Event implements StoppableEventInterface
{
    use Stoppable;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var null|string
     */
    protected $method;

    /**
     * @var array Data
     */
    protected array $context;

    public function __construct(Model $model, ?string $method = null)
    {
        $this->model = $model;
        $this->method = $method ?? \lcfirst(\class_basename(static::class));
        $this->context = request_context()->toArray();
    }

    public function __wakeup()
    {
        RequestContext::createFromArray($this->context);
    }

    public function handle()
    {
        if (\method_exists($this->getModel(), $this->getMethod())) {
            $this->getModel()->{$this->getMethod()}($this);
        }

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get the remote event.
     */
    public function transformToRemote()
    {
        $name = Str::snake($this->getMethod());
        $name = str_replace('_', '.', $name);
        return new RemoteEvent($name, $this->getModel(), []);
    }

    /**
     * Get the event method.
     */
    public function transformToQueue()
    {
        $name = Str::snake($this->getMethod());
        $name = str_replace('_', '.', $name);
        return new QueueEvent($name, $this->getModel(), []);
    }
}
