<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource;

use Hyperf\Utils\Coroutine;
use Hyperf\Kafka\ConsumerManager;
use Hyperf\Event\ListenerProvider;
use Tusimo\Resource\Job\ResourceConsumer;
use Psr\Http\Message\ServerRequestInterface;
use Tusimo\Resource\Listener\FetchModeListener;
use Tusimo\Resource\Listener\InitCacheListener;
use Tusimo\Resource\Collector\ListenerCollector;
use Tusimo\Resource\Listener\ModelEventListener;
use Tusimo\Resource\Listener\ModelChangedListener;
use Tusimo\Resource\Generator\ResourceDeleteCommand;
use Tusimo\Resource\Listener\ModelHookEventListener;
use Tusimo\Resource\Generator\ResourceGenerateCommand;
use Hyperf\Kafka\Listener\BeforeMainServerStartListener;
use Tusimo\Resource\Listener\RemoteResourceChangedListener;
use Tusimo\Resource\Middleware\InitRequestContextMiddleware;
use Tusimo\Resource\Crontab\Process\CrontabDispatcherProcess;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                ServerRequestInterface::class => \Tusimo\Resource\Entity\ResourceRequest::class,
            ],
            'commands' => [
                ResourceGenerateCommand::class,
                ResourceDeleteCommand::class,
            ],
            'listeners' => [
                FetchModeListener::class,
                ModelEventListener::class,
                ModelHookEventListener::class => 99,
                ModelChangedListener::class,
                RemoteResourceChangedListener::class,
                InitCacheListener::class,
            ],
            'processes' => [
                ResourceConsumer::class,
                CrontabDispatcherProcess::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'collectors' => [
                        ListenerCollector::class,
                    ],
                    'class_map' => [
                        Coroutine::class => __DIR__ . '/../class_map/Hyperf/Utils/Coroutine.php',
                        ListenerProvider::class => __DIR__ . '/../class_map/Hyperf/Event/ListenerProvider.php',
                        BeforeMainServerStartListener::class => __DIR__ . '/../class_map/Hyperf/Kafka/Listener/BeforeMainServerStartListener.php',
                        ConsumerManager::class => __DIR__ . '/../class_map/Hyperf/Kafka/ConsumerManager.php',
                    ],
                ],
            ],
            'middlewares' => [
                'http' => [
                    InitRequestContextMiddleware::class,
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for resourceable.',
                    'source' => __DIR__ . '/../publish/resourceable.php',
                    'destination' => BASE_PATH . '/config/autoload/resourceable.php',
                ],
            ],
        ];
    }
}
