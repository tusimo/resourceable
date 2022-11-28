<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Concerns;

use Hyperf\Redis\Redis;
use Hyperf\Cache\CacheManager;
use Hyperf\Redis\RedisFactory;
use Hyperf\Crontab\LoggerInterface;
use Tusimo\Resource\Repository\DbRepository;
use Tusimo\Resource\Repository\ApiRepository;
use Tusimo\Resource\Repository\NullRepository;
use Tusimo\Resource\Contract\ResourceCacheAble;
use Tusimo\Resource\Filter\BitFilter\BitFilter;
use Tusimo\Resource\Repository\CacheRepository;
use Tusimo\Resource\Repository\ModelRepository;
use Tusimo\Resource\Filter\Bits\RedisBitHandler;
use Tusimo\Resource\Resolver\PoolClientResolver;
use Tusimo\Resource\Repository\Cache\MemoryCache;
use Tusimo\Resource\Repository\Cache\StorageCache;
use Tusimo\Resource\Filter\BloomFilter\BloomFilter;
use Tusimo\Resource\Resolver\ContextHeaderResolver;
use Tusimo\Resource\Repository\Cache\RedisHashCache;
use Tusimo\Resource\Repository\CollectionRepository;
use Tusimo\Resource\Repository\Cache\LRUResourceCache;
use Tusimo\Resource\Repository\Cache\SwooleTableCache;
use Tusimo\Resource\Contract\ResourceRepositoryContract;
use Tusimo\Resource\Repository\FilterWithExistsRepository;
use Tusimo\Resource\Repository\FilterWithNotExistsRepository;

trait HasRepository
{
    /**
     * use repository.
     * @var ResourceRepositoryContract
     * @return static
     */
    protected function useRepo(ResourceRepositoryContract $repo)
    {
        $this->setRepository($repo);
        return $this;
    }

    protected function nullRepository(): NullRepository
    {
        return new NullRepository();
    }

    protected function modelRepository(string $modelClass): ModelRepository
    {
        return (new ModelRepository())->setModelClass($modelClass);
    }

    protected function dbRepository(string $table, string $keyName = 'id', string $connectionName = 'default'): DbRepository
    {
        return (new DbRepository())->setTable($table)
            ->setKeyName($keyName)
            ->setConnectionName($connectionName);
    }

    protected function apiRepository(string $service, string $apiVersion = 'v2', string $version = 'v2'): ApiRepository
    {
        return (new ApiRepository(
            config('services.' . $service),
            $this->getResourceName(),
            $version,
            new ContextHeaderResolver(),
            new PoolClientResolver(),
            container()->get(LoggerInterface::class),
        ))->setApiVersion($apiVersion);
    }

    protected function lruCacheRepository(ResourceRepositoryContract $repo): CacheRepository
    {
        return $this->cacheRepository($repo, $this->lruCacheInstance());
    }

    protected function memoryCacheRepository(ResourceRepositoryContract $repo): CacheRepository
    {
        return $this->cacheRepository($repo, $this->memoryCacheInstance());
    }

    protected function storageCacheRepository(ResourceRepositoryContract $repo): CacheRepository
    {
        return $this->cacheRepository($repo, $this->storageCacheInstance());
    }

    protected function swooleTableCacheRepository(ResourceRepositoryContract $repo): CacheRepository
    {
        return $this->cacheRepository($repo, $this->swooleTableCacheInstance());
    }

    protected function redisHashCacheRepository(ResourceRepositoryContract $repo): CacheRepository
    {
        return $this->cacheRepository($repo, $this->redisHashCacheInstance());
    }

    /**
     * New a cache repository.
     */
    protected function cacheRepository(
        ResourceRepositoryContract $repository,
        ResourceCacheAble $cache = null
    ): CacheRepository {
        return new CacheRepository(
            $repository,
            $this->getResourceName(),
            $cache,
            $this->getKeyName(),
        );
    }

    /**
     * New a collection repository.
     */
    protected function collectionRepository(): CollectionRepository
    {
        return new CollectionRepository(
            $this->getResourceName(),
            $this->getKeyName(),
            $this->getKeyType()
        );
    }

    protected function lruCacheInstance(): LRUResourceCache
    {
        return new LRUResourceCache($this->getResourceName(), $this->getKeyName());
    }

    /**
     * New a storage cache
     * this cache will be persisted into the cache driver.
     * @param string $driver
     */
    protected function storageCacheInstance($driver = 'default'): StorageCache
    {
        return new StorageCache(make(CacheManager::class)->getDriver($driver), $this->getResourceName(), $this->getKeyName());
    }

    protected function getRedisInstance($driver = 'default'): Redis
    {
        return make(RedisFactory::class)->get($driver);
    }

    /**
     * New a swoole table cache
     * This cache can be used in all process,it is a memory share cache between all process.
     * this cache will be lost when process restart.
     */
    protected function swooleTableCacheInstance(): SwooleTableCache
    {
        return new SwooleTableCache($this->getResourceName(), $this->getKeyName());
    }

    /**
     * New a memory cache instance.
     * This is only can be used in single process,it is not shared by multi process.
     * this cache will be lost when process restart.
     */
    protected function memoryCacheInstance(): MemoryCache
    {
        return new MemoryCache($this->getResourceName(), $this->getKeyName());
    }

    protected function redisHashCacheInstance(string $driver = 'default'): RedisHashCache
    {
        return new RedisHashCache($this->getRedisInstance($driver), $this->getResourceName(), $this->getKeyName());
    }

    /**
     * BitFilterRepository
     * filter store the not exists
     * Notice: this filter repository does not need initialize the filter data.
     */
    protected function bitFilterRepository(ResourceRepositoryContract $repository, int $min = 0, int $max = 65535): FilterWithNotExistsRepository
    {
        $redis = container()->get(\Hyperf\Redis\Redis::class);
        $driver = new RedisBitHandler($redis);
        $filter = new BitFilter($driver, 'filters:' . $this->getResourceName(), $min, $max);
        return new FilterWithNotExistsRepository($repository, $this->getResourceName(), $filter, $this->getKeyName());
    }

    /**
     * BloomFilterRepository
     * filter store the exists resource
     * Notice: this filter repository should initialize the filter data before use.
     */
    protected function bloomFilterRepository(ResourceRepositoryContract $repository, int $size = 65535): FilterWithExistsRepository
    {
        $redis = container()->get(\Hyperf\Redis\Redis::class);
        $driver = new RedisBitHandler($redis);
        $filter = new BloomFilter($driver, 'filters:' . $this->getResourceName(), $size);
        return new FilterWithExistsRepository($repository, $this->getResourceName(), $filter, $this->getKeyName());
    }
}
