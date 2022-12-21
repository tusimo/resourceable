<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Annotation;

use Attribute;
use Hyperf\Utils\Arr;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Tusimo\Resource\Collector\ListenerCollector;

/**
 * @codingStandardsIgnoreFile
 * @Annotation
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class ModelListener extends AbstractAnnotation
{
    /**
     * @var array
     */
    public $models = [];

    public function __construct(...$value)
    {
        parent::__construct(...$value);

        if ($formattedValue = $this->formatParams($value)['value'] ?? null) {
            if (is_string($formattedValue)) {
                $this->models = [$formattedValue];
            } elseif (is_array($formattedValue) && ! Arr::isAssoc($formattedValue)) {
                $this->models = $formattedValue;
            }
        }
    }

    public function collectClass(string $className): void
    {
        parent::collectClass($className);

        foreach ($this->models as $model) {
            ListenerCollector::register($model, $className);
        }
    }
}
