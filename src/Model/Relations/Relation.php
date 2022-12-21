<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Model\Relations;

use Hyperf\Utils\Arr;
use Hyperf\Macroable\Macroable;
use Tusimo\Resource\Model\Model;
use Tusimo\Resource\Model\Builder;
use Tusimo\Resource\Model\Collection;
use Hyperf\Utils\Traits\ForwardsCalls;

/**
 * @mixin \Tusimo\Resource\Model\Builder
 */
abstract class Relation
{
    use ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * The Model query builder instance.
     *
     * @var \Tusimo\Resource\Model\Builder
     */
    protected $query;

    /**
     * The parent model instance.
     *
     * @var \Tusimo\Resource\Model\Model
     */
    protected $parent;

    /**
     * The related model instance.
     *
     * @var \Tusimo\Resource\Model\Model
     */
    protected $related;

    protected $retrieveFromModels = false;

    /**
     * Create a new relation instance.
     */
    public function __construct(Builder $query, Model $parent)
    {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();

        $this->addConstraints();
    }

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param string $method
     * @param array $parameters
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $result = $this->forwardCallTo($this->query, $method, $parameters);

        if ($result === $this->query) {
            return $this;
        }

        return $result;
    }

    /**
     * Force a clone of the underlying query builder when cloning.
     */
    public function __clone()
    {
        $this->query = clone $this->query;
    }

    /**
     * Run a callback with constraints disabled on the relation.
     */
    public static function noConstraints(\Closure $callback)
    {
        $previous = Constraint::isConstraint();

        Constraint::setConstraint(false);

        // When resetting the relation where clause, we want to shift the first element
        // off of the bindings, leaving only the constraints that the developers put
        // as "extra" on the relationships, and not original relation constraints.
        try {
            return call_user_func($callback);
        } finally {
            Constraint::setConstraint($previous);
        }
    }

    /**
     * Set the base constraints on the relation query.
     */
    abstract public function addConstraints();

    /**
     * Set the constraints for an eager load of the relation.
     */
    abstract public function addEagerConstraints(array $models);

    /**
     * Initialize the relation on a set of models.
     *
     * @param string $relation
     * @return array
     */
    abstract public function initRelation(array $models, $relation);

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param string $relation
     * @return array
     */
    abstract public function match(array $models, Collection $results, $relation);

    /**
     * Get the results of the relationship.
     */
    abstract public function getResults();

    /**
     * Get the relationship for eager loading.
     *
     * @return \Tusimo\Resource\Model\Collection
     */
    public function getEager()
    {
        if ($this->retrieveFromModels) {
            return new Collection();
        }
        return $this->get();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     * @return \Tusimo\Resource\Model\Collection
     */
    public function get($columns = ['*'])
    {
        return $this->query->get($columns);
    }

    /**
     * Touch all of the related models for the relationship.
     */
    public function touch()
    {
        $model = $this->getRelated();

        if (! $model::isIgnoringTouch()) {
            $this->rawUpdate([
                $model->getUpdatedAtColumn() => $model->freshTimestampString(),
            ]);
        }
    }

    /**
     * Run a raw update against the base query.
     *
     * @return int
     */
    public function rawUpdate(array $attributes = [])
    {
        return $this->query->withoutGlobalScopes()->update($attributes);
    }

    /**
     * Add the constraints for an internal relationship existence query.
     *
     * Essentially, these queries compare on column names like whereColumn.
     *
     * @param array|mixed $columns
     * @return \Tusimo\Resource\Model\Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return $query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(),
            '=',
            $this->getExistenceCompareKey()
        );
    }

    /**
     * Get the underlying query for the relation.
     *
     * @return \Tusimo\Resource\Model\Builder
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get the parent model of the relation.
     *
     * @return \Tusimo\Resource\Model\Model
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get the fully qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName()
    {
        return $this->parent->getQualifiedKeyName();
    }

    /**
     * Get the related model of the relation.
     *
     * @return \Tusimo\Resource\Model\Model
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function createdAt()
    {
        return $this->parent->getCreatedAtColumn();
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function updatedAt()
    {
        return $this->parent->getUpdatedAtColumn();
    }

    /**
     * Get the name of the related model's "updated at" column.
     *
     * @return string
     */
    public function relatedUpdatedAt()
    {
        return $this->related->getUpdatedAtColumn();
    }

    public function preloadRelations(array $models, string $relationName): array
    {
        /**
         * @var Model $mod
         */
        $mod = Arr::first($models);
        $relations = [];
        if ($mod->hasAttribute($relationName)) {// already load the relation
            $isCollection = false;
            if ($mod->getRelation($relationName) instanceof Collection) {
                $isCollection = true;
            }
            /**
             * @var Model $model
             */
            foreach ($models as $model) {
                if ($isCollection) {
                    if (! empty($model->getOriginal($relationName))) {
                        $model->setRelation(
                            $relationName,
                            $this->query->hydrate($model->getOriginal($relationName))
                        );
                        $relations = array_merge(
                            $relations,
                            $model->getRelation($relationName)->all()
                        );
                    }
                } else {
                    if (! is_null($model->getOriginal($relationName))) {
                        $model->setRelation(
                            $relationName,
                            $this->query->getModel()->newFromBuilder($model->getOriginal($relationName))
                        );
                        $relations[] = $model->getRelation($relationName);
                    }
                }
                $model->removeAttribute($relationName);
                $model->syncOriginal();
            }
            if (! empty($relations)) {
                $this->query->eagerLoadRelations($relations);
            }
        }
        return $models;
    }

    public function setRetrieveFromModels($flag = true): self
    {
        $this->retrieveFromModels = $flag;
        return $this;
    }

    public function matching(array $models, Collection $results, $relation)
    {
        if ($this->retrieveFromModels) {
            return $this->preloadRelations($models, $relation);
        }
        return $this->match($models, $results, $relation);
    }

    /**
     * Get all of the primary keys for an array of models.
     *
     * @param string $key
     * @return array
     */
    protected function getKeys(array $models, $key = null)
    {
        return collect($models)->map(function ($value) use ($key) {
            return $key ? $value->getAttribute($key) : $value->getKey();
        })->values()->unique(null, true)->sort()->all();
    }

    /**
     * Get the name of the "where in" method for eager loading.
     *
     * @param string $key
     * @return string
     */
    protected function whereInMethod(Model $model, $key)
    {
        return $model->getKeyName() === last(explode('.', $key))
                    && $model->getIncrementing()
                    && in_array($model->getKeyType(), ['int', 'integer'])
                        ? 'whereIn'
                        : 'whereIn';
    }
}
