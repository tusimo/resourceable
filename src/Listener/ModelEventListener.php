<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Listener;

use Psr\Container\ContainerInterface;
use Tusimo\Resource\Model\Events\Event;
use Hyperf\Event\Contract\ListenerInterface;
use Tusimo\Resource\Collector\ListenerCollector;

class ModelEventListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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
        $model = $event->getModel();
        $modelName = get_class($model);

        $listeners = ListenerCollector::getListenersForModel($modelName);
        foreach ($listeners as $name) {
            if (! $this->container->has($name)) {
                continue;
            }

            $listener = $this->container->get($name);
            if (method_exists($listener, $event->getMethod())) {
                $listener->{$event->getMethod()}($event);
            }
        }
    }
}
