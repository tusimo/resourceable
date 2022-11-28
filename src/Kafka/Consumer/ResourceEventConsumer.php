<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace App\Kafka\Consumer;

use Hyperf\Kafka\Annotation\Consumer;
use Psr\Container\ContainerInterface;
use Tusimo\Resource\Kafka\Base\Event;
use Tusimo\Resource\Kafka\Base\EventConsumer;
use Tusimo\Resource\Kafka\Base\ResourceEvent;
use longlang\phpkafka\Consumer\ConsumeMessage;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tusimo\Resource\Model\Events\Event as EventsEvent;

/**
 * @Consumer
 */
class ResourceEventConsumer extends EventConsumer
{
    /**
     * Undocumented variable.
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    protected $eventPattern = [
        'remote.created',
        'remote.updated',
        'remote.deleted',
        'remote.restored',
        'remote.force_deleted',
        'remote.saved',
    ];

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    public function consumeEvent(Event $event, ConsumeMessage $message)
    {
        $resourceEvent = ResourceEvent::createFromBase($event);
        $modelEvent = $resourceEvent->getModelEvent();
        if ($modelEvent instanceof EventsEvent) {
            $this->dispatcher->dispatch($modelEvent);
        }
    }
}
