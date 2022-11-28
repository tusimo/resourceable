<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Kafka\Base;

use Hyperf\Kafka\Producer as HyperfProducer;
use longlang\phpkafka\Producer\ProduceMessage;

class EventProducer extends HyperfProducer
{
    public function broadcastEvent(Event $event, string $topic = 'message_buses')
    {
        $this->send($topic, serialize($event), $event->getKey());
    }

    public function broadcastEvents(array $events, string $topic = 'message_buses')
    {
        $messages = [];
        foreach ($events as $event) {
            $messages[] = new ProduceMessage(
                $topic,
                $event->__toString(),
                $event->getKey()
            );
        }
        $this->sendBatch($messages);
    }

    public function broadcast(
        string $eventName,
        $data,
        string $key = '',
        array $meta = [],
        string $topic = 'message_buses'
    ) {
        $event = new Event();
        $event->setName($eventName)
            ->setKey($key)->setData($data)
            ->setMeta($meta)->setRequestContext(request_context());
        $this->broadcastEvent($event, $topic);
    }
}
