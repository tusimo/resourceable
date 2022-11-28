<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class SubDbRepository extends SubDbTableRepository
{
    /**
     * 分表的函数.
     */
    protected $subTableResolver;

    /**
     * container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ConfigInterface.
     *
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);
    }

    protected function initConnectionConfig(string $connectionName)
    {
        if ($this->hasConnectionName($connectionName)) {
            return;
        }
        // we will use a connection as template and auto generate the sub db connection config.
        $connectionConfig = $this->getConnectionConfig($this->getConnectionName());
        $connectionConfig['database'] = $connectionName;
        $this->config->set('databases.' . $connectionName, $connectionConfig);
    }

    protected function hasConnectionName(string $connectionName): bool
    {
        return ! empty($this->getConnectionConfig($connectionName));
    }

    protected function getSubConnectionNameByKey($key): string
    {
        $connectionName = parent::getSubConnectionNameByKey($key);
        $this->initConnectionConfig($connectionName);
        return $connectionName;
    }

    protected function getConnectionConfig(string $connectionName): array
    {
        return $this->config->get('databases.' . $connectionName, []);
    }
}
