<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Crontab\Process;

use Hyperf\Process\ProcessManager;
use Hyperf\Process\AbstractProcess;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Tusimo\Resource\Crontab\Scheduler;
use Hyperf\Utils\Coordinator\Constants;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Strategy\StrategyInterface;
use Hyperf\Utils\Coordinator\CoordinatorManager;
use Hyperf\Crontab\Event\CrontabDispatcherStarted;

class CrontabDispatcherProcess extends AbstractProcess
{
    /**
     * @var string
     */
    public $name = 'crontab-dispatcher';

    /**
     * @var Server
     */
    private $server;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
        $this->scheduler = $container->get(Scheduler::class);
        $this->strategy = $container->get(StrategyInterface::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function bind($server): void
    {
        $this->server = $server;
        parent::bind($server);
    }

    public function isEnable($server): bool
    {
        return $this->config->get('crontab.enable', false);
    }

    public function handle(): void
    {
        $this->event->dispatch(new CrontabDispatcherStarted());
        while (ProcessManager::isRunning()) {
            $crontabs = $this->scheduler->schedule();
            while (! $crontabs->isEmpty()) {
                $crontab = $crontabs->dequeue();
                $this->strategy->dispatch($crontab);
            }
            if ($this->sleep()) {
                break;
            }
        }
    }

    /**
     * @return bool whether the server shutdown
     */
    private function sleep(): bool
    {
        $current = date('s', time());
        $sleep = 60 - $current;
        $this->logger->debug('Crontab dispatcher sleep ' . $sleep . 's.');
        if ($sleep > 0) {
            if (CoordinatorManager::until(Constants::WORKER_EXIT)->yield($sleep)) {
                return true;
            }
        }

        return false;
    }
}
