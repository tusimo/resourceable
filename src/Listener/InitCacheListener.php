<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Listener;

use Psr\Log\LoggerInterface;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Tusimo\Resource\Model\Events\Event;
use Tusimo\Resource\Job\Base\QueueTrait;
use Tusimo\Resource\Utils\LRUCacheManager;
use Hyperf\Event\Contract\ListenerInterface;
use Tusimo\Resource\Utils\SwooleTableManager;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Server\Event\MainCoroutineServerStart;
use Tusimo\Resource\Repository\Cache\LRUResourceCache;
use Tusimo\Resource\Repository\Cache\SwooleTableCache;

class InitCacheListener implements ListenerInterface
{
    use QueueTrait;

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

    /**
     * Undocumented variable.
     *
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerInterface::class);
        $this->config = $container->get(ConfigInterface::class);
    }

    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
            MainCoroutineServerStart::class,
        ];
    }

    /**
     * @param Event $event
     */
    public function process(object $event)
    {
        if ($this->config->get('resourceable.repository.cache.table.capacity', 0) > 0) {
            $table = SwooleTableManager::initTable(
                SwooleTableCache::CACHE_KEY,
                $this->config->get('resourceable.repository.cache.table.capacity', 0),
                $this->config->get('resourceable.repository.cache.table.size', 1024)
            );
            $info = [
                'size' => $table->getSize(),
                'data_size' => $table->getDataSize(),
                'memory_size' => formatBytes($table->getMemorySize()),
            ];

            $this->logger->info('Init Swoole Table Cache.', $info);
        }

        if ($this->config->get('resourceable.repository.cache.lru.capacity', 0) > 0) {
            $cache = LRUCacheManager::initLRUCache(LRUResourceCache::CACHE_KEY);
            $info = [
                'size' => $cache->capacity(),
                'data_size' => $cache->size(),
            ];
            $this->logger->info('Init LRU Cache.', $info);
        }
    }
}
