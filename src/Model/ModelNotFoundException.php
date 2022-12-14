<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model;

use Hyperf\Utils\Arr;

class ModelNotFoundException extends \RuntimeException
{
    /**
     * Name of the affected Model model.
     *
     * @var string
     */
    protected $model;

    /**
     * The affected model IDs.
     *
     * @var array|int
     */
    protected $ids;

    /**
     * Set the affected Model model and instance ids.
     *
     * @param string $model
     * @param array|int $ids
     * @return $this
     */
    public function setModel($model, $ids = [])
    {
        $this->model = $model;
        $this->ids = Arr::wrap($ids);

        $this->message = "No query results for model [{$model}]";

        if (count($this->ids) > 0) {
            $this->message .= ' ' . implode(', ', $this->ids);
        } else {
            $this->message .= '.';
        }

        return $this;
    }

    /**
     * Get the affected Model model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the affected Model model IDs.
     *
     * @return array|int
     */
    public function getIds()
    {
        return $this->ids;
    }
}
