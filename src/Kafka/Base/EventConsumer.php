<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Kafka\Base;

use Psr\Log\LoggerInterface;
use Hyperf\Kafka\AbstractConsumer;
use Psr\Container\ContainerInterface;
use longlang\phpkafka\Consumer\ConsumeMessage;

abstract class EventConsumer extends AbstractConsumer
{
    public $topic = 'message_buses';

    public $groupId;

    /**
     * Undocumented variable.
     */
    protected LoggerInterface $logger;

    protected ContainerInterface $container;

    /**
     * event pattern.
     *
     * @var array|string
     */
    protected $eventPattern = '*';

    public function __construct(ContainerInterface $container)
    {
        $this->logger = $container->get(LoggerInterface::class);
        $this->container = $container;
        if (is_null($this->groupId)) {
            if (is_local() || is_development()) {
                $this->groupId = env('APP_ENV', 'local') . '-' .
                    env('APP_NAME', 'php-template');
                $this->groupInstanceId = $this->groupId . '-instance';
            }
        }
    }

    public function consume(ConsumeMessage $message)
    {
        $event = unserialize($message->getValue());
        if ($event->getName() === '') {
            $this->logger
                ->warning(
                    'event unserialize error:',
                    [$message->getTopic(), $message->getValue()]
                );
            return;
        }
        if (! $this->matchEvent($event)) {
            $this->logger
                ->info('event miss match, ignore', [$event->getName(), $event->getKey()]);
            return;
        }
        $this->consumeEvent($event, $message);
    }

    abstract public function consumeEvent(Event $event, ConsumeMessage $message);

    protected function matchEvent(Event $event): bool
    {
        if ($this->eventPattern === '*') {
            return true;
        }
        if ($this->eventPattern === $event->getName()) {
            return true;
        }
        $patterns = $this->eventPattern;
        if (! is_array($this->eventPattern)) {
            $patterns = [$patterns];
        }
        foreach ($patterns as $pattern) {
            $pattern = str_replace(['.', '*'], ['\.', '.*'], $pattern);
            $pattern = "/{$pattern}/";
            if (preg_match($pattern, $event->getName())) {
                return true;
            }
        }

        return false;
    }
}
