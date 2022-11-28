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
use Hyperf\Event\Contract\ListenerInterface;
use Tusimo\Resource\Model\Events\RemoteEvent;
use Tusimo\Resource\Contract\ResourceCleanAble;
use Tusimo\Resource\Model\Events\RemoteCreated;
use Tusimo\Resource\Model\Events\RemoteDeleted;
use Tusimo\Resource\Model\Events\RemoteUpdated;
use Tusimo\Resource\Contract\RepositoryProxyAble;

class RemoteResourceChangedListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Undocumented variable.
     *
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerInterface::class);
    }

    public function listen(): array
    {
        return [
            RemoteCreated::class,
            RemoteUpdated::class,
            RemoteDeleted::class,
        ];
    }

    /**
     * @param Event $event
     */
    public function process(object $event)
    {
        if ($event instanceof RemoteEvent) {
            $model = $event->getModel();
            $repository = $model->getRepository();
            if ($repository instanceof ResourceCleanAble) {
                $repository->shouldClean() && $repository->deleteResourceCache($model->getKey());
            }
            // if repository is proxy repository, we will try to delete all cache from the proxy repository
            while ($repository instanceof RepositoryProxyAble) {
                $repository = $repository->getRepository();
                if ($repository instanceof ResourceCleanAble) {
                    $repository->shouldClean() && $repository->deleteResourceCache($model->getKey());
                }
            }
        }
    }
}
