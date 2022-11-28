<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Kafka\Base;

use Tusimo\Resource\Model\Events\RemoteEvent;
use Tusimo\Resource\Model\Events\Event as ModelEvent;

class ResourceEvent extends Event
{
    /**
     * Undocumented function.
     *
     * @return static
     */
    public static function createFromRemoteEvent(RemoteEvent $event)
    {
        $model = $event->getModel();
        $instance = new static();
        $instance->setName($event->getName());
        $instance->setKey($model->getKey() . '');
        $instance->setMeta([
            'class' => get_class($event),
        ]);
        $instance->setData($event);
        $instance->setRequestContext(request_context());
        return $instance;
    }

    /**
     * Undocumented function.
     *
     * @return null|ModelEvent
     */
    public function getModelEvent()
    {
        $class = $this->getMeta('class');
        if (! $class) {
            return null;
        }

        return $this->getData();
    }

    public static function createFromBase(Event $event)
    {
        $instance = new static();
        $instance->setData($event->getData());
        $instance->setKey($event->getKey());
        $instance->setMeta($event->getMeta());
        $instance->setName($event->getName());
        $instance->setRequestContext($event->getRequestContext());
        return $instance;
    }
}
