<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Listener;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Tusimo\Resource\Model\Events\Event;
use Tusimo\Resource\Model\Events\Saved;
use Tusimo\Resource\Model\Events\Created;
use Tusimo\Resource\Model\Events\Deleted;
use Tusimo\Resource\Model\Events\Updated;
use Tusimo\Resource\Model\Events\Restored;
use Hyperf\Event\Contract\ListenerInterface;
use Tusimo\Resource\Kafka\Base\EventProducer;
use Tusimo\Resource\Kafka\Base\ResourceEvent;
use Tusimo\Resource\Model\Events\CustomEvent;
use Tusimo\Resource\Model\Events\RemoteSaved;
use Tusimo\Resource\Model\Events\ForceDeleted;
use Tusimo\Resource\Model\Events\RemoteCreated;
use Tusimo\Resource\Model\Events\RemoteDeleted;
use Tusimo\Resource\Model\Events\RemoteUpdated;
use Tusimo\Resource\Model\Events\RemoteRestored;
use Tusimo\Resource\Model\Events\RemoteForceDeleted;

class ModelChangedListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EventProducer
     */
    protected $producer;

    /**
     * Undocumented variable.
     *
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->producer = $container->get(EventProducer::class);
        $this->logger = $container->get(LoggerInterface::class);
    }

    public function listen(): array
    {
        return [
            Created::class,
            Updated::class,
            Deleted::class,
            Saved::class,
            Restored::class,
            ForceDeleted::class,
            CustomEvent::class,
        ];
    }

    /**
     * @param Event $event
     */
    public function process(object $event)
    {
        if ($event instanceof Event) {
            $model = $event->getModel();
            if ($model->shouldBroadcastEvent($event)) {
                $remoteEvent = $this->transformEventToRemote($event);

                $resourceEvent = ResourceEvent::createFromRemoteEvent($remoteEvent);
                $this->producer->broadcastEvent($resourceEvent);
                $this->logger->info("resource event: {$remoteEvent->getName()} send to broadcast system success", [
                    'name' => $remoteEvent->getName(),
                    'method' => $remoteEvent->getMethod(),
                    'resource' => $remoteEvent->getModel()->getResourceName(),
                    'key' => $remoteEvent->getModel()->getKey(),
                ]);
            }
        }
    }

    private function transformEventToRemote(Event $event)
    {
        if ($event instanceof Created) {
            return new RemoteCreated('created', $event->getModel());
        }
        if ($event instanceof Updated) {
            return new RemoteUpdated('updated', $event->getModel());
        }
        if ($event instanceof Deleted) {
            return new RemoteDeleted('deleted', $event->getModel());
        }
        if ($event instanceof Saved) {
            return new RemoteSaved('saved', $event->getModel());
        }
        if ($event instanceof Restored) {
            return new RemoteRestored('restored', $event->getModel());
        }
        if ($event instanceof ForceDeleted) {
            return new RemoteForceDeleted('force_deleted', $event->getModel());
        }
        return $event->transformToRemote();
    }
}
