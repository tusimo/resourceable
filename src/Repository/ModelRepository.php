<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Repository;

use Hyperf\Database\Model\Model;
use Hyperf\Database\Query\Builder;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class ModelRepository extends DbRepository implements ResourceRepositoryContract
{
    protected string $modelClass;

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    public function setModelClass(string $modelClass)
    {
        $this->modelClass = $modelClass;
        return $this;
    }

    /**
     * Get the value of keyName.
     */
    public function getKeyName()
    {
        return $this->getModel()->getKeyName();
    }

    /**
     * Model.
     */
    protected function getModel(): Model
    {
        return new ($this->getModelClass());
    }

    /**
     * Get Builder.
     *
     * @return Builder
     */
    protected function getBuilder()
    {
        return $this->getModel()->newQuery()->getQuery();
    }
}
