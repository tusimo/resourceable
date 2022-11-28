<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Kafka\Base;

use Hyperf\Di\Annotation\Inject;

trait EventProducerTrait
{
    /**
     * Event Producer.
     *
     * @Inject()
     * @var EventProducer
     */
    protected $eventProducer;
}
