<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model;

use Hyperf\Contract\UnCompressInterface;

class ModelStatusMeta extends ModelMeta implements UnCompressInterface
{
    public $status = [];

    /**
     * @param int|string $key
     */
    public function __construct(Model $model)
    {
        parent::__construct(get_class($model), $model->getKey());
        $this->status = [
            'attributes' => $model->getAttributes(),
            'original' => $model->getOriginal(),
            'exists' => $model->exists,
            'wasRecentlyCreated' => $model->wasRecentlyCreated,
        ];
    }

    public function uncompress()
    {
        /**
         * @var Model
         */
        $model = new $this->class();
        if ($this->status) {
            $model->setRawAttributes($this->status['attributes']);
            $model->setOriginal($this->status['original']);
            $model->syncChanges();
            $model->exists = $this->status['exists'];
            $model->wasRecentlyCreated = $this->status['wasRecentlyCreated'];
        }
        return $model;
    }
}
