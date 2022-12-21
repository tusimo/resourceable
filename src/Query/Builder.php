<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
namespace Tusimo\Resource\Query;

use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use Tusimo\Restable\Query;
use Hyperf\Utils\Collection;
use Tusimo\Restable\QueryItem;
use Tusimo\Restable\QueryWith;
use Hyperf\Macroable\Macroable;
use Hyperf\Paginator\Paginator;
use Tusimo\Resource\Model\Model;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Traits\ForwardsCalls;
use Hyperf\Contract\PaginatorInterface;
use Hyperf\Paginator\LengthAwarePaginator;
use Tusimo\Resource\Model\Concerns\BuildsQueries;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Tusimo\Resource\Contract\ResourceRepositoryContract;

class Builder extends Query
{
    use BuildsQueries, ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * Model.
     *
     * @var Model
     */
    protected $model;

    /**
     * Handle dynamic method calls into the method.
     *
     * @param string $method
     * @param array $parameters
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }
        if (Str::startsWith($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }

        static::throwBadMethodCallException($method);
    }

    /**
     * Set Builder From an exists Query.
     * @return static
     */
    public function setBuilderFromQuery(Query $query)
    {
        if ($query->hasQueryOrderBy()) {
            $this->setQueryAggregate($query->getQueryAggregate());
        }
        if ($query->hasQueryOrderBy()) {
            $this->setQueryOrderBy($query->getQueryOrderBy());
        }
        if ($query->hasQueryPagination()) {
            $this->setQueryPagination($query->getQueryPagination());
        }
        if ($query->hasQuerySeek()) {
            $this->setQuerySeek($query->getQuerySeek());
        }
        $this->setParameters($query->getParameters());
        $this->setQueryItems($query->getQueryItems());
        $this->setQuerySelect($query->getQuerySelect());
        $this->setQueryWith($this->getQueryWith());
        return $this;
    }

    /**
     * Get Repository.
     *
     * @return ResourceRepositoryContract
     */
    public function getRepository()
    {
        return $this->getModel()->getRepository();
    }

    /**
     * Set the columns to be selected.
     *
     * @param array|mixed $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        return parent::select($columns);
    }

    /**
     * Add a subselect expression to the query.
     *
     * @param \Closure|string|\Tusimo\Resource\Query\Builder $query
     * @param string $as
     * @throws \InvalidArgumentException
     */
    public function selectSub($query, $as)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a new "raw" select expression to the query.
     *
     * @param string $expression
     */
    public function selectRaw($expression, array $bindings = [])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Makes "from" fetch from a subquery.
     *
     * @param \Closure|string|\Tusimo\Resource\Query\Builder $query
     * @param string $as
     * @throws \InvalidArgumentException
     */
    public function fromSub($query, $as)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a raw from clause to the query.
     *
     * @param string $expression
     * @param mixed $bindings
     */
    public function fromRaw($expression, $bindings = [])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a new select column to the query.
     *
     * @param array|mixed $column
     * @return $this
     */
    public function addSelect($column)
    {
        $column = is_array($column) ? $column : func_get_args();

        return $this->select($column);
    }

    /**
     * Force the query to only return distinct results.
     */
    public function distinct()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Set the table which the query is targeting.
     *
     * @param string $table
     */
    public function from($table)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Set the force indexes which the query should be used.
     */
    public function forceIndexes(array $forceIndexes)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a join clause to the query.
     *
     * @param string $table
     * @param \Closure|string $first
     * @param null|string $operator
     * @param null|string $second
     * @param string $type
     * @param bool $where
     */
    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "join where" clause to the query.
     *
     * @param string $table
     * @param \Closure|string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     */
    public function joinWhere($table, $first, $operator, $second, $type = 'inner')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a subquery join clause to the query.
     *
     * @param \Closure|string|\Tusimo\Resource\Query\Builder $query
     * @param string $as
     * @param \Closure|string $first
     * @param null|string $operator
     * @param null|string $second
     * @param string $type
     * @param bool $where
     * @throws \InvalidArgumentException
     */
    public function joinSub($query, $as, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a left join to the query.
     *
     * @param string $table
     * @param \Closure|string $first
     * @param null|string $operator
     * @param null|string $second
     */
    public function leftJoin($table, $first, $operator = null, $second = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "join where" clause to the query.
     *
     * @param string $table
     * @param \Closure|string $first
     * @param string $operator
     * @param string $second
     */
    public function leftJoinWhere($table, $first, $operator, $second)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a subquery left join to the query.
     *
     * @param \Closure|string|\Tusimo\Resource\Query\Builder $query
     * @param string $as
     * @param \Closure|string $first
     * @param null|string $operator
     * @param null|string $second
     */
    public function leftJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a right join to the query.
     *
     * @param string $table
     * @param \Closure|string $first
     * @param null|string $operator
     * @param null|string $second
     */
    public function rightJoin($table, $first, $operator = null, $second = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "right join where" clause to the query.
     *
     * @param string $table
     * @param \Closure|string $first
     * @param string $operator
     * @param string $second
     */
    public function rightJoinWhere($table, $first, $operator, $second)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a subquery right join to the query.
     *
     * @param \Closure|string|\Tusimo\Resource\Query\Builder $query
     * @param string $as
     * @param \Closure|string $first
     * @param null|string $operator
     * @param null|string $second
     */
    public function rightJoinSub($query, $as, $first, $operator = null, $second = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "cross join" clause to the query.
     *
     * @param string $table
     * @param null|\Closure|string $first
     * @param null|string $operator
     * @param null|string $second
     */
    public function crossJoin($table, $first = null, $operator = null, $second = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Merge an array of where clauses and bindings.
     *
     * @param array $wheres
     * @param array $bindings
     */
    public function mergeWheres($wheres, $bindings)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param array|\Closure|string $column
     * @param string $boolean
     * @param null|mixed $operator
     * @param null|mixed $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->checkMethodArgsSupported(__FUNCTION__, 3, func_num_args());
        if (is_array($column)) {
            foreach ($column as $key => $value) {
                parent::where($key, $value);
            }
        } else {
            parent::where($column, $operator, $value);
        }

        return $this;
    }

    /**
     * Prepare the value and operator for a where clause.
     *
     * @param string $value
     * @param string $operator
     * @param bool $useDefault
     * @throws \InvalidArgumentException
     */
    public function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param array|\Closure|string $column
     * @param null|mixed $operator
     * @param null|mixed $value
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where" clause comparing two columns to the query.
     *
     * @param array|string $first
     * @param null|string $operator
     * @param null|string $second
     * @param null|string $boolean
     */
    public function whereColumn($first, $operator = null, $second = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where" clause comparing two columns to the query.
     *
     * @param array|string $first
     * @param null|string $operator
     * @param null|string $second
     */
    public function orWhereColumn($first, $operator = null, $second = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a raw where clause to the query.
     *
     * @param string $sql
     * @param string $boolean
     * @param mixed $bindings
     */
    public function whereRaw($sql, $bindings = [], $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a raw or where clause to the query.
     *
     * @param string $sql
     * @param mixed $bindings
     */
    public function orWhereRaw($sql, $bindings = [])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @param mixed $values
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->checkMethodArgsSupported(__FUNCTION__, 2, func_num_args());

        return parent::whereIn($column, $values);
    }

    /**
     * Add an "or where in" clause to the query.
     *
     * @param string $column
     * @param mixed $values
     */
    public function orWhereIn($column, $values)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where not in" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param mixed $values
     */
    public function whereNotIn($column, $values, $boolean = 'and')
    {
        $this->checkMethodArgsSupported(__FUNCTION__, 2, func_num_args());

        return parent::whereNotIn($column, $values);
    }

    /**
     * Add an "or where not in" clause to the query.
     *
     * @param string $column
     * @param mixed $values
     */
    public function orWhereNotIn($column, $values)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where in raw" clause for integer values to the query.
     *
     * @param string $column
     * @param array|Arrayable $values
     * @param string $boolean
     * @param bool $not
     */
    public function whereIntegerInRaw($column, $values, $boolean = 'and', $not = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where not in raw" clause for integer values to the query.
     *
     * @param string $column
     * @param array|Arrayable $values
     * @param string $boolean
     */
    public function whereIntegerNotInRaw($column, $values, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param array|string $columns
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereNull($columns, $boolean = 'and', $not = false)
    {
        $this->checkMethodArgsSupported(__FUNCTION__, 1, func_num_args());

        foreach (Arr::wrap($columns) as $column) {
            parent::whereNull($column);
        }

        return $this;
    }

    /**
     * Add an "or where null" clause to the query.
     *
     * @param string $column
     */
    public function orWhereNull($column)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @return static|\Tusimo\Resource\Query\Builder
     */
    public function whereNotNull($column, $boolean = 'and')
    {
        $this->checkMethodArgsSupported(__FUNCTION__, 1, func_num_args());

        return parent::whereNotNull($column);
    }

    /**
     * Add a where between statement to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @return $this
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $this->checkMethodArgsSupported(__FUNCTION__, 2, func_num_args());

        return parent::whereBetween($column, $values);
    }

    /**
     * Add an or where between statement to the query.
     *
     * @param string $column
     */
    public function orWhereBetween($column, array $values)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a where not between statement to the query.
     *
     * @param string $column
     * @param string $boolean
     * @return static|\Tusimo\Resource\Query\Builder
     */
    public function whereNotBetween($column, array $values, $boolean = 'and')
    {
        $this->checkMethodArgsSupported(__FUNCTION__, 2, func_num_args());

        return parent::whereNotBetween($column, $values);
    }

    /**
     * Add an or where not between statement to the query.
     *
     * @param string $column
     */
    public function orWhereNotBetween($column, array $values)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where not null" clause to the query.
     *
     * @param string $column
     */
    public function orWhereNotNull($column)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where date" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     * @param string $boolean
     */
    public function whereDate($column, $operator, $value = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where date" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     */
    public function orWhereDate($column, $operator, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where time" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     * @param string $boolean
     */
    public function whereTime($column, $operator, $value = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where time" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     */
    public function orWhereTime($column, $operator, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where day" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     * @param string $boolean
     */
    public function whereDay($column, $operator, $value = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where day" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     */
    public function orWhereDay($column, $operator, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where month" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     * @param string $boolean
     */
    public function whereMonth($column, $operator, $value = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where month" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|string $value
     */
    public function orWhereMonth($column, $operator, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where year" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|int|string $value
     * @param string $boolean
     */
    public function whereYear($column, $operator, $value = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "or where year" statement to the query.
     *
     * @param string $column
     * @param string $operator
     * @param \DateTimeInterface|int|string $value
     */
    public function orWhereYear($column, $operator, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a nested where statement to the query.
     *
     * @param string $boolean
     */
    public function whereNested(\Closure $callback, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Create a new query instance for nested where condition.
     */
    public function forNestedWhere()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param static|\Tusimo\Resource\Query\Builder $query
     * @param string $boolean
     */
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an exists clause to the query.
     *
     * @param string $boolean
     * @param bool $not
     */
    public function whereExists(\Closure $callback, $boolean = 'and', $not = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an or exists clause to the query.
     *
     * @param bool $not
     */
    public function orWhereExists(\Closure $callback, $not = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a where not exists clause to the query.
     *
     * @param string $boolean
     */
    public function whereNotExists(\Closure $callback, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a where not exists clause to the query.
     */
    public function orWhereNotExists(\Closure $callback)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an exists clause to the query.
     *
     * @param \Tusimo\Resource\Query\Builder $query
     * @param string $boolean
     * @param bool $not
     */
    public function addWhereExistsQuery(self $query, $boolean = 'and', $not = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Adds a where condition using row values.
     *
     * @param array $columns
     * @param string $operator
     * @param array $values
     * @param string $boolean
     */
    public function whereRowValues($columns, $operator, $values, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Adds a or where condition using row values.
     *
     * @param array $columns
     * @param string $operator
     * @param array $values
     */
    public function orWhereRowValues($columns, $operator, $values)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where JSON contains" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param bool $not
     * @param mixed $value
     */
    public function whereJsonContains($column, $value, $boolean = 'and', $not = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "or where JSON contains" clause to the query.
     *
     * @param string $column
     * @param mixed $value
     */
    public function orWhereJsonContains($column, $value)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where JSON not contains" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param mixed $value
     */
    public function whereJsonDoesntContain($column, $value, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "or where JSON not contains" clause to the query.
     *
     * @param string $column
     * @param mixed $value
     */
    public function orWhereJsonDoesntContain($column, $value)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "where JSON length" clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param null|mixed $value
     * @param mixed $operator
     */
    public function whereJsonLength($column, $operator, $value = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "or where JSON length" clause to the query.
     *
     * @param string $column
     * @param null|mixed $value
     * @param mixed $operator
     */
    public function orWhereJsonLength($column, $operator, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Handles dynamic "where" clauses to the query.
     *
     * @param string $method
     * @param array $parameters
     * @return $this
     */
    public function dynamicWhere($method, $parameters)
    {
        $finder = substr($method, 5);

        $segments = preg_split('/(And)(?=[A-Z])/', $finder, -1, PREG_SPLIT_DELIM_CAPTURE);

        $index = 0;

        foreach ($segments as $segment) {
            $this->addDynamic($segment, $parameters, $index);

            ++$index;
        }
        return $this;
    }

    /**
     * Add a "group by" clause to the query.
     *
     * @param array ...$groups
     */
    public function groupBy(...$groups)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "having" clause to the query.
     *
     * @param string $column
     * @param null|string $operator
     * @param null|string $value
     * @param string $boolean
     */
    public function having($column, $operator = null, $value = null, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "or having" clause to the query.
     *
     * @param string $column
     * @param null|string $operator
     * @param null|string $value
     */
    public function orHaving($column, $operator = null, $value = null)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a "having between " clause to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param bool $not
     */
    public function havingBetween($column, array $values, $boolean = 'and', $not = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a raw having clause to the query.
     *
     * @param string $sql
     * @param string $boolean
     */
    public function havingRaw($sql, array $bindings = [], $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a raw or having clause to the query.
     *
     * @param string $sql
     */
    public function orHavingRaw($sql, array $bindings = [])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param string $column
     * @param string $direction
     */
    public function orderBy($column, $direction = 'asc')
    {
        return parent::orderBy($column, strtolower($direction) === 'asc' ? 'asc' : 'desc');
    }

    /**
     * Add a descending "order by" clause to the query.
     *
     * @param string $column
     * @return $this
     */
    public function orderByDesc($column)
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     * @return static|\Tusimo\Resource\Query\Builder
     */
    public function latest($column = 'created_at')
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Add an "order by" clause for a timestamp to the query.
     *
     * @param string $column
     * @return static|\Tusimo\Resource\Query\Builder
     */
    public function oldest($column = 'created_at')
    {
        return $this->orderBy($column, 'asc');
    }

    /**
     * Put the query's results in random order.
     *
     * @param string $seed
     */
    public function inRandomOrder($seed = '')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a raw "order by" clause to the query.
     *
     * @param string $sql
     * @param array $bindings
     */
    public function orderByRaw($sql, $bindings = [])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Alias to set the "offset" value of the query.
     *
     * @param int $value
     * @return static|\Tusimo\Resource\Query\Builder
     */
    public function skip($value)
    {
        return $this->offset($value);
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param int $value
     * @return $this
     */
    public function offset($value)
    {
        // $property = $this->unions ? 'unionOffset' : 'offset';

        // $this->{$property} = max(0, $value);

        return parent::offset(max(0, $value));
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param int $value
     * @return static|\Tusimo\Resource\Query\Builder
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $value
     * @return $this
     */
    public function limit($value)
    {
        if ($value >= 0) {
            return parent::limit($value);
        }

        return $this;
    }

    /**
     * Set the limit and offset for a given page.
     *
     * @param int $page
     * @param int $perPage
     * @return static|\Tusimo\Resource\Query\Builder
     */
    public function forPage($page, $perPage = 15)
    {
        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }

    /**
     * Constrain the query to the previous "page" of results before a given ID.
     *
     * @param int $perPage
     * @param null|int $lastId
     * @param string $column
     * @return $this
     */
    public function forPageBeforeId($perPage = 15, $lastId = 0, $column = 'id')
    {
        // $this->orders = $this->removeExistingOrdersFor($column);
        $this->queryOrderBy = null;

        if (! is_null($lastId)) {
            $this->where($column, '<', $lastId);
        }

        return $this->orderBy($column, 'desc')->limit($perPage);
    }

    /**
     * Constrain the query to the next "page" of results after a given ID.
     *
     * @param int $perPage
     * @param null|int $lastId
     * @param string $column
     * @return $this
     */
    public function forPageAfterId($perPage = 15, $lastId = 0, $column = 'id')
    {
        // $this->orders = $this->removeExistingOrdersFor($column);
        $this->queryOrderBy = null;

        if (! is_null($lastId)) {
            $this->where($column, '>', $lastId);
        }

        return $this->orderBy($column, 'asc')->limit($perPage);
    }

    /**
     * Add a union statement to the query.
     *
     * @param \Closure|\Tusimo\Resource\Query\Builder $query
     * @param bool $all
     */
    public function union($query, $all = false)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a union all statement to the query.
     *
     * @param \Closure|\Tusimo\Resource\Query\Builder $query
     */
    public function unionAll($query)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Lock the selected rows in the table.
     *
     * @param bool|string $value
     */
    public function lock($value = true)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Lock the selected rows in the table for updating.
     */
    public function lockForUpdate()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Share lock the selected rows in the table.
     */
    public function sharedLock()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get the SQL representation of the query.
     */
    public function toSql()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Execute a query for a single record by ID.
     *
     * @param mixed $id
     * @param array $columns
     * @return mixed|static
     */
    public function find($id, $columns = ['*'])
    {
        return $this->where('id', '=', $id)->first($columns);
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param string $column
     */
    public function value($column)
    {
        $result = (array) $this->first([$column]);

        return count($result) > 0 ? reset($result) : null;
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array|string $columns
     */
    public function get($columns = ['*']): Collection
    {
        return collect($this->onceWithColumns(Arr::wrap($columns), function () {
            return $this->getResources();
        }));
    }

    public function getByPrimaryKey($key): array
    {
        return $this->getRepository()
            ->get($key, $this->getQuerySelect()->getSelects(), $this->getQueryWith()->getWith());
    }

    public function getByPrimaryKeys(array $keys): array
    {
        return $this->getRepository()
            ->getByIds($keys, $this->getQuerySelect()->getSelects(), $this->getQueryWith()->getWith());
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param null|int $page
     */
    public function simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null): PaginatorInterface
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        return $this->simplePaginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @param int $perPage
     * @param string[] $columns
     * @param string $pageName
     * @param null $page
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginatorInterface
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $total = $this->getCountForPagination();
        $results = $total ? $this->forPage($page, $perPage)->get($columns) : collect();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Get the count of the total records for the paginator.
     *
     * @param array $columns
     * @return int
     */
    public function getCountForPagination($columns = ['*'])
    {
        $clone = $this->cloneForPaginationCount();
        return $clone->count();
    }

    /**
     * Get a generator for the given query.
     */
    public function cursor()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Chunk the results of a query by comparing numeric IDs.
     *
     * @param int $count
     * @param string $column
     * @param null|string $alias
     * @return bool
     */
    public function chunkById($count, callable $callback, $column = 'id', $alias = null)
    {
        $alias = $alias ?: $column;

        $lastId = null;

        do {
            $clone = clone $this;

            // We'll execute the query for the given page and get the results. If there are
            // no results we can just break and return from here. When there are results
            // we will call the callback with the current chunk of these results here.
            $results = $clone->forPageAfterId($count, $lastId, $column)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if ($callback($results) === false) {
                return false;
            }

            $lastResult = $results->last();
            $lastId = is_array($lastResult) ? $lastResult[$alias] : $lastResult->{$alias};

            unset($results);
        } while ($countResults == $count);

        return true;
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param string $column
     * @param null|string $key
     * @return \Hyperf\Utils\Collection
     */
    public function pluck($column, $key = null)
    {
        // First, we will need to select the results of the query accounting for the
        // given columns / key. Once we have the results, we will be able to take
        // the results and get the exact data that was requested for the query.
        $queryResult = $this->onceWithColumns(is_null($key) ? [$column] : [$column, $key], function () {
            return $this->getRepository()->getByQuery($this);
        });

        if (empty($queryResult)) {
            return collect();
        }

        // If the columns are qualified with a table or have an alias, we cannot use
        // those directly in the "pluck" operations since the results from the DB
        // are only keyed by the column itself. We'll strip the table out here.
        $column = $this->stripTableForPluck($column);

        $key = $this->stripTableForPluck($key);

        return is_array($queryResult[0]) ? $this->pluckFromArrayColumn($queryResult, $column, $key) : $this->pluckFromObjectColumn($queryResult, $column, $key);
    }

    /**
     * Concatenate values of a given column as a string.
     *
     * @param string $column
     * @param string $glue
     * @return string
     */
    public function implode($column, $glue = '')
    {
        return $this->pluck($column)->implode($glue);
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->count() > 0;
    }

    /**
     * Determine if no rows exist for the current query.
     *
     * @return bool
     */
    public function doesntExist()
    {
        return ! $this->exists();
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param string $columns
     * @return int
     */
    public function count($columns = '*')
    {
        return (int) $this->aggregate(__FUNCTION__, Arr::wrap($columns));
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param string $column
     */
    public function min($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the maximum value of a given column.
     *
     * @param string $column
     */
    public function max($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param string $column
     */
    public function sum($column)
    {
        $result = $this->aggregate(__FUNCTION__, [$column]);

        return $result ?: 0;
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param string $column
     */
    public function avg($column)
    {
        return $this->aggregate(__FUNCTION__, [$column]);
    }

    /**
     * Alias for the "avg" method.
     *
     * @param string $column
     */
    public function average($column)
    {
        return $this->avg($column);
    }

    /**
     * Execute an aggregate function on the database.
     *
     * @param string $function
     * @param array $columns
     */
    public function aggregate($function, $columns = ['*'])
    {
        $this->withAggregates($function, $columns);
        $result = $this->getRepository()->aggregate($this);
        return $result[$function][Arr::first($columns)] ?? 0;
    }

    /**
     * Execute a numeric aggregate function on the database.
     *
     * @param string $function
     * @param array $columns
     * @return float|int
     */
    public function numericAggregate($function, $columns = ['*'])
    {
        $result = $this->aggregate($function, $columns);

        // If there is no result, we can obviously just return 0 here. Next, we will check
        // if the result is an integer or float. If it is already one of these two data
        // types we can just return the result as-is, otherwise we will convert this.
        if (! $result) {
            return 0;
        }

        if (is_int($result) || is_float($result)) {
            return $result;
        }

        // If the result doesn't contain a decimal place, we will assume it is an int then
        // cast it to one. When it does we will cast it to a float since it needs to be
        // cast to the expected data type for the developers out of pure convenience.
        return strpos((string) $result, '.') === false ? (int) $result : (float) $result;
    }

    /**
     * Insert a new record into the database.
     *
     * @return bool
     */
    public function insert(array $values)
    {
        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient when building these
        // inserts statements by verifying these elements are actually an array.
        if (empty($values)) {
            return true;
        }

        if (! is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }
        $result = $this->getRepository()->add(Arr::first($values));
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Insert a new record into the database.
     *
     * @return array
     */
    public function batchInsert(array $values)
    {
        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient when building these
        // inserts statements by verifying these elements are actually an array.
        if (empty($values)) {
            return true;
        }

        if (! is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }
        return $this->getRepository()->batchAdd($values);
    }

    /**
     * Insert a new record and get the value of the primary key.
     *
     * @param null|string $sequence
     * @return int
     */
    public function insertGetId(array $values, $sequence = null)
    {
        // Since every insert gets treated like a batch insert, we will make sure the
        // bindings are structured in a way that is convenient when building these
        // inserts statements by verifying these elements are actually an array.
        if (empty($values)) {
            return true;
        }

        if (! is_array(reset($values))) {
            $values = [$values];
        } else {
            foreach ($values as $key => $value) {
                ksort($value);

                $values[$key] = $value;
            }
        }

        $result = $this->getRepository()->add(Arr::first($values));
        return $result[$this->getModel()->getKeyName()] ?? 0;
    }

    /**
     * Insert new records into the table using a subquery.
     *
     * @param \Closure|string|\Tusimo\Resource\Query\Builder $query
     */
    public function insertUsing(array $columns, $query)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Insert ignore a new record into the database.
     */
    public function insertOrIgnore(array $values)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Update a record in the database.
     *
     * @return int
     */
    public function update(array $values)
    {
        $where = $this->getCorrectPrimaryKeyWhere();
        if (is_null($where)) {
            return 0;
        }
        if ($where->isOperation('eq')) {
            $result = $this->getRepository()->update($where->getValue(), $values);
            return count($result) == 1;
        }

        // batch values
        $records = [];
        foreach ($where->getValue() as $value) {
            $records[] = array_merge($values, [$this->getKeyName() => $value]);
        }

        // batch Update
        $result = $this->getRepository()->batchUpdate($records);
        return count($result);
    }

    /**
     * Update a record in the database.
     *
     * @return array
     */
    public function batchUpdate(array $values)
    {
        $where = $this->getCorrectPrimaryKeyWhere();
        if (count($this->getQueryItems()) > 0) {
            throw new \RuntimeException('batch update can not use where');
        }
        $records = [];
        foreach ($values as $value) {
            if (isset($value[$this->getKeyName()])) {
                $records[] = $value;
            }
        }
        // only support Key Update
        return $this->getRepository()->batchUpdate($records);
    }

    /**
     * Insert or update a record matching the attributes, and fill it with values.
     *
     * @return bool
     */
    public function updateOrInsert(array $attributes, array $values = [])
    {
        $exists = $this->where($attributes)->first([$this->getKeyName()]);
        if (empty($exists)) {
            return $this->insert(array_merge($attributes, $values));
        }

        return (bool) $this->newQuery()
            ->where($this->getKeyName(), $exists[$this->getKeyName()])
            ->update($values);
    }

    /**
     * Increment a column's value by a given amount.
     *
     * @param string $column
     * @param float|int $amount
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Decrement a column's value by a given amount.
     *
     * @param string $column
     * @param float|int $amount
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Delete a record from the database.
     *
     * @param null|mixed $id
     * @return int
     */
    public function delete($id = null)
    {
        if (! is_null($id)) {
            $this->where($this->getKeyName(), $id);
        }
        $where = $this->getCorrectPrimaryKeyWhere();
        if (is_null($where)) {
            return 0;
        }
        if ($where->isOperation('eq')) {
            $result = $this->getRepository()->delete($where->getValue());
            return $result ? 1 : 0;
        }
        // batch delete
        return $this->getRepository()->deleteByIds($where->getValue());
    }

    /**
     * Run a truncate statement on the table.
     */
    public function truncate()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Tusimo\Resource\Query\Builder
     */
    public function newQuery()
    {
        return (new static())->setModel($this->getModel());
    }

    /**
     * Create a raw database expression.
     *
     * @param mixed $value
     */
    public function raw($value)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get the current query value bindings in a flattened array.
     */
    public function getBindings()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get the raw array of bindings.
     */
    public function getRawBindings()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Set the bindings on the query builder.
     *
     * @param string $type
     * @throws \InvalidArgumentException
     */
    public function setBindings(array $bindings, $type = 'where')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a binding to the query.
     *
     * @param string $type
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function addBinding($value, $type = 'where')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Merge an array of bindings into our bindings.
     *
     * @param \Tusimo\Resource\Query\Builder $query
     */
    public function mergeBindings(self $query)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get the database connection instance.
     */
    public function getConnection()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get the database query processor instance.
     */
    public function getProcessor()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get the query grammar instance.
     */
    public function getGrammar()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Use the write pdo for query.
     */
    public function useWritePdo()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Clone the query without the given properties.
     *
     * @return static
     */
    public function cloneWithout(array $properties)
    {
        return tap(clone $this, function ($clone) use ($properties) {
            foreach ($properties as $property) {
                $clone->{$property} = null;
            }
        });
    }

    /**
     * Clone the query without the given bindings.
     */
    public function cloneWithoutBindings(array $except)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Get model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set model.
     *
     * @param Model $model Model
     *
     * @return self
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function withAll(array $all = []): self
    {
        $this->setQueryWith(new QueryWith($all));
        return $this;
    }

    protected function throwMethodNotSupportedException($method)
    {
        throw new \RuntimeException('method not supported yet: ' . $method);
    }

    protected function checkMethodArgsSupported($method, $supportNum, $actualNum)
    {
        if ($supportNum >= $actualNum) {
            return;
        }
        throw new \RuntimeException("method: {$method} arg not matched, support:{$supportNum}, actual:{$actualNum} ");
    }

    protected function getResources(): array
    {
        if (! $this->canGetResourceThroughPrimaryKeys()) {
            return $this->getRepository()->getByQuery($this);
        }
        if ($where = $this->getCorrectPrimaryKeyWhere()) {
            if ($where->isOperation('eq')) {
                $resource = $this->getByPrimaryKey($where->getValue());
                if (! $resource) {
                    return [];
                }
                return [$resource];
            }
            if ($where->isOperation('in')) {
                return $this->getByPrimaryKeys($where->getValue());
            }
        }
        return [];
    }

    /**
     * Check the query items to see if they only contains primary keys find.
     */
    protected function canGetResourceThroughPrimaryKeys(): bool
    {
        // seek does not match
        if ($this->hasQuerySeek()) {
            if ($this->getQuerySeek()->hasOffset()) {
                return false;
            }
        }
        if ($this->hasQueryPagination()) {
            return false;
        }
        if ($this->hasQueryAggregate()) {
            return false;
        }
        if ($this->hasQueryOrderBy()) {
            return false;
        }
        if (empty($this->getQueryItems())) {
            return false;
        }
        foreach ($this->getQueryItems() as $queryItem) {
            if ($queryItem->getName() != $this->getKeyName()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get Where operation eq key value.
     */
    protected function getWhereKeyEqValue()
    {
        $whereEq = $this->getPrimaryKeyWhereEq();
        if ($whereEq) {
            return $whereEq->getValue();
        }
        return null;
    }

    /**
     * Get all primary key QueryItems.
     *
     * @return QueryItem[]
     */
    protected function getAllPrimaryKeyWhere(): array
    {
        $queryItems = [];
        foreach ($this->getQueryItems() as $queryItem) {
            if ($queryItem->getName() == $this->getModel()->getKeyName()) {
                $queryItems[] = $queryItem;
            }
        }
        return $queryItems;
    }

    protected function isPrimaryKeyWhereAble(): bool
    {
        foreach ($this->getQueryItems() as $queryItem) {
            if ($queryItem->getName() != $this->getModel()->getKeyName()) {
                return false;
            }
            if (! $queryItem->isOperation('eq') && ! $queryItem->isOperation('in')) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get Correct primary key QueryItem.
     *
     * @return null|QueryItem[]
     */
    protected function getPrimaryKeyWhereEqs()
    {
        $whereEqs = [];
        foreach ($this->getAllPrimaryKeyWhere() as $queryItem) {
            if ($queryItem->isOperation('eq')) {
                $whereEqs[] = $queryItem;
            }
        }
        return $whereEqs;
    }

    /**
     * Get Correct primary key QueryItem.
     *
     * @return null|QueryItem[]
     */
    protected function getPrimaryKeyWhereIns()
    {
        $whereIns = [];
        foreach ($this->getAllPrimaryKeyWhere() as $queryItem) {
            if ($queryItem->isOperation('in')) {
                $whereIns[] = $queryItem;
            }
        }
        return $whereIns;
    }

    /**
     * Get Correct primary key QueryItem,
     * It should be one eq QueryItem or one in QueryItem.
     *
     * @return null|QueryItem
     */
    protected function getCorrectPrimaryKeyWhere()
    {
        if (! $this->isPrimaryKeyWhereAble()) {
            return null;
        }
        $whereIns = $this->getPrimaryKeyWhereIns();
        $whereEqs = $this->getPrimaryKeyWhereEqs();
        $newWhereEq = null;
        // get new whereEq from whereEqs,
        // if has multi whereEq, only correct where all whereEq is with the same value
        // if not,we just return null
        foreach ($whereEqs as $queryItem) {
            if ($newWhereEq == null) {
                $newWhereEq = $queryItem;
                continue;
            }
            // we check if current queryItem is the same with previous one
            if ($newWhereEq->getValue() != $queryItem->getValue()) {
                return null;
            }
        }
        // Flatten whereIns into One
        $inValues = [];
        foreach ($whereIns as $queryItem) {
            $inValues += $queryItem->getValue();
        }
        // if inValues empty, just return the newWhereEq
        if (empty($inValues)) {
            return $newWhereEq;
        }
        // if newWhereEq is null, we just build a new whereIn and return it
        if (is_null($newWhereEq)) {
            return new QueryItem($this->getKeyName(), 'in', $inValues);
        }
        // if whereEq value is in whereIn values, return whereEq value
        if (in_array($newWhereEq->getValue(), $inValues)) {
            return $newWhereEq;
        }
        // finally, the whereIn can not use with whereEq
        return null;
    }

    /**
     * Get Resource Key Name.
     */
    protected function getKeyName(): string
    {
        return $this->getModel()->getKeyName();
    }

    /**
     * Creates a subquery and parse it.
     *
     * @param \Closure|string|\Tusimo\Resource\Query\Builder $query
     */
    protected function createSub($query)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Parse the subquery into SQL and bindings.
     *
     * @param mixed $query
     */
    protected function parseSub($query)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an array of where clauses to the query.
     *
     * @param array $column
     * @param string $boolean
     * @param string $method
     */
    protected function addArrayOfWheres($column, $boolean, $method = 'where')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Determine if the given operator and value combination is legal.
     * Prevents using Null values with invalid operators.
     *
     * @param string $operator
     * @param mixed $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        // return is_null($value) && in_array($operator, $this->operators) && ! in_array($operator, ['=', '<>', '!=']);
        return false;
    }

    /**
     * Determine if the given operator is supported.
     *
     * @param string $operator
     * @return bool
     */
    protected function invalidOperator($operator)
    {
        // return ! in_array(strtolower($operator), $this->operators, true) && ! in_array(strtolower($operator), $this->grammar->getOperators(), true);
        return false;
    }

    /**
     * Add a where in with a sub-select to the query.
     *
     * @param string $column
     * @param string $boolean
     * @param bool $not
     */
    protected function whereInSub($column, \Closure $callback, $boolean, $not)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add an external sub-select to the query.
     *
     * @param string $column
     * @param static|\Tusimo\Resource\Query\Builder $query
     * @param string $boolean
     * @param bool $not
     */
    protected function whereInExistingQuery($column, $query, $boolean, $not)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a date based (year, month, day, time) statement to the query.
     *
     * @param string $type
     * @param string $column
     * @param string $operator
     * @param string $boolean
     * @param mixed $value
     */
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and')
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a full sub-select to the query.
     *
     * @param string $column
     * @param string $operator
     * @param string $boolean
     */
    protected function whereSub($column, $operator, \Closure $callback, $boolean)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Add a single dynamic where clause statement to the query.
     *
     * @param string $segment
     * @param string $connector
     * @param array $parameters
     * @param int $index
     */
    protected function addDynamic($segment, $parameters, $index)
    {
        $this->where(Str::snake($segment), '=', $parameters[$index]);
    }

    /**
     * Get an array with all orders with a given column removed.
     *
     * @param string $column
     * @return array
     */
    protected function removeExistingOrdersFor($column)
    {
        return [];
    }

    /**
     * Run the query as a "select" statement against the connection.
     */
    protected function runSelect()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Clone the existing query instance for usage in a pagination subquery.
     *
     * @return self
     */
    protected function cloneForPaginationCount()
    {
        return $this->cloneWithout(['queryAggregate', 'queryOrderBy', 'queryWith']);
    }

    /**
     * Run a pagination count query.
     *
     * @param array $columns
     */
    protected function runPaginationCountQuery($columns = ['*'])
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Remove the column aliases since they will break count queries.
     */
    protected function withoutSelectAliases(array $columns)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Throw an exception if the query doesn't have an orderBy clause.
     *
     * @throws \RuntimeException
     */
    protected function enforceOrderBy()
    {
        if (! $this->hasQueryOrderBy()) {
            throw new \RuntimeException('You must specify an orderBy clause when using this function.');
        }
    }

    /**
     * Strip off the table name or alias from a column identifier.
     *
     * @param string $column
     * @return null|string
     */
    protected function stripTableForPluck($column)
    {
        return is_null($column) ? $column : last(preg_split('~\.| ~', $column));
    }

    /**
     * Retrieve column values from rows represented as objects.
     *
     * @param array $queryResult
     * @param string $column
     * @param string $key
     * @return \Hyperf\Utils\Collection
     */
    protected function pluckFromObjectColumn($queryResult, $column, $key)
    {
        $results = [];

        if (is_null($key)) {
            foreach ($queryResult as $row) {
                $results[] = $row->{$column};
            }
        } else {
            foreach ($queryResult as $row) {
                $results[$row->{$key}] = $row->{$column};
            }
        }

        return collect($results);
    }

    /**
     * Retrieve column values from rows represented as arrays.
     *
     * @param array $queryResult
     * @param string $column
     * @param string $key
     * @return \Hyperf\Utils\Collection
     */
    protected function pluckFromArrayColumn($queryResult, $column, $key)
    {
        $results = [];

        if (is_null($key)) {
            foreach ($queryResult as $row) {
                $results[] = $row[$column];
            }
        } else {
            foreach ($queryResult as $row) {
                $results[$row[$key]] = $row[$column];
            }
        }

        return collect($results);
    }

    /**
     * Set the aggregate property without running the query.
     *
     * @param string $function
     * @param array $columns
     */
    protected function setAggregate($function, $columns)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Execute the given callback while selecting the given columns.
     * After running the callback, the columns are reset to the original value.
     *
     * @param array $columns
     * @param callable $callback
     */
    protected function onceWithColumns($columns, $callback)
    {
        $original = $this->getQuerySelect()->getSelects();

        if (empty($original)) {
            $this->select($columns);
        }

        $result = $callback();

        $this->getQuerySelect()->setSelects($original);

        return $result;
    }

    /**
     * Create a new query instance for a sub-query.
     */
    protected function forSubQuery()
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Remove all of the expressions from a list of bindings.
     */
    protected function cleanBindings(array $bindings)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }

    /**
     * Create a new length-aware paginator instance.
     */
    protected function paginator(Collection $items, int $total, int $perPage, int $currentPage, array $options): LengthAwarePaginatorInterface
    {
        $container = ApplicationContext::getContainer();
        if (! method_exists($container, 'make')) {
            return new LengthAwarePaginator($items, $total, $perPage, $currentPage, $options);
            throw new \RuntimeException('The DI container does not support make() method.');
        }
        return $container->make(LengthAwarePaginatorInterface::class, compact('items', 'total', 'perPage', 'currentPage', 'options'));
    }

    /**
     * Create a new simple paginator instance.
     */
    protected function simplePaginator(Collection $items, int $perPage, int $currentPage, array $options): PaginatorInterface
    {
        $container = ApplicationContext::getContainer();
        if (! method_exists($container, 'make')) {
            throw new \RuntimeException('The DI container does not support make() method.');
        }
        return $container->make(PaginatorInterface::class, compact('items', 'perPage', 'currentPage', 'options'));
    }

    /**
     * Assert the value for bindings.
     *
     * @param mixed $value
     * @param string $column
     */
    protected function assertBinding($value, $column = '', int $limit = 0)
    {
        $this->throwMethodNotSupportedException(__FUNCTION__);
    }
}
