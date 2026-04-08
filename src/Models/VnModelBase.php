<?php

namespace VnCoder\Models;
use Illuminate\Database\Eloquent\Model;
use VnCoder\Core\Query\QueryBuilderWithCache;

/**
 *
 * @property int $id
 * @property string $table
 * @property array $fillable
 * @property array $rules
 * @method static \Illuminate\Database\Eloquent\Builder|$this select(array|mixed ...$columns = ['*']) Set the columns to be selected.
 * @method static \Illuminate\Database\Eloquent\Builder|$this insertGetId(array $data) insert record and Get Id
 * @method static \Illuminate\Database\Eloquent\Builder|$this selectRaw(string $expression, array $bindings = []) Add a new "raw" select expression to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this fromRaw(string $expression, mixed $bindings = []) Add a raw from clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this addSelect(array|mixed $column) Add a new select column to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this distinct() Force the query to only return distinct results.
 * @method static \Illuminate\Database\Eloquent\Builder|$this from(\Closure|\Illuminate\Database\Eloquent\Builder|string $table, string|null $as = null) Set the table which the query is targeting.
 * @method static \Illuminate\Database\Eloquent\Builder|$this join(string $table, \Closure|string $first, string|null $operator = null, string|null $second = null, string $type = 'inner', bool $where = false) Add a join clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this joinWhere(string $table, \Closure|string $first, string $operator, string $second, string $type = 'inner') Add a "join where" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this joinSub(\Closure|\Illuminate\Database\Eloquent\Builder|string $query, string $as, \Closure|string $first, string|null $operator = null, string|null $second = null, string $type = 'inner', bool $where = false) Add a sub-query join clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this leftJoin(string $table, \Closure|string $first, string|null $operator = null, string|null $second = null) Add a left join to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this leftJoinWhere(string $table, \Closure|string $first, string $operator, string $second) Add a "join where" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this leftJoinSub(\Closure|\Illuminate\Database\Eloquent\Builder|string $query, string $as, \Closure|string $first, string|null $operator = null, string|null $second = null) Add a sub-query left join to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this rightJoin(string $table, \Closure|string $first, string|null $operator = null, string|null $second = null) Add a right join to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this rightJoinWhere(string $table, \Closure|string $first, string $operator, string $second) Add a "right join where" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this rightJoinSub(\Closure|\Illuminate\Database\Eloquent\Builder|string $query, string $as, \Closure|string $first, string|null $operator = null, string|null $second = null) Add a sub-query right join to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this crossJoin(string $table, \Closure|string|null $first = null, string|null $operator = null, string|null $second = null) Add a "cross join" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this crossJoinSub(\Closure|\Illuminate\Database\Eloquent\Builder|string $query, string $as) Add a sub-query cross join to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this where(\Closure|string|array $column, mixed $operator = null, mixed $value = null, string $boolean = 'and') Add a basic where clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this addArrayOfWheres(array $column, string $boolean, string $method = 'where') Add an array of where clauses to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhere(\Closure|string|array $column, mixed $operator = null, mixed $value = null) Add an "or where" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNot(\Closure|string|array $column, mixed $operator = null, mixed $value = null, string $boolean = 'and') Add a basic "where not" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNot(\Closure|string|array $column, mixed $operator = null, mixed $value = null) Add an "or where not" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereColumn(string|array $first, string|null $operator = null, string|null $second = null, string|null $boolean = 'and') Add a "where" clause comparing two columns to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereColumn(string|array $first, string|null $operator = null, string|null $second = null) Add an "or where" clause comparing two columns to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereRaw(string $sql, mixed $bindings = [], string $boolean = 'and') Add a raw where clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereRaw(string $sql, mixed $bindings = []) Add a raw or where clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereIn(string $column, mixed $values, string $boolean = 'and', bool $not = false) Add a "where in" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereIn(string $column, mixed $values) Add an "or where in" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotIn(string $column, mixed $values, string $boolean = 'and') Add a "where not in" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotIn(string $column, mixed $values) Add an "or where not in" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereIntegerInRaw(string $column, array $values, string $boolean = 'and', bool $not = false) Add a "where in raw" clause for integer values to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereIntegerInRaw(string $column, array $values) Add an "or where in raw" clause for integer values to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereIntegerNotInRaw(string $column, array $values, string $boolean = 'and') Add a "where not in raw" clause for integer values to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereIntegerNotInRaw(string $column, array $values) Add an "or where not in raw" clause for integer values to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNull(string|array $columns, string $boolean = 'and', bool $not = false) Add a "where null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNull(string|array $column) Add an "or where null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotNull(string|array $columns, string $boolean = 'and') Add a "where not null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereBetween(string $column, iterable $values, string $boolean = 'and', bool $not = false) Add a where between statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereBetweenColumns(string $column, array $values, string $boolean = 'and', bool $not = false) Add a where between statement using columns to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereBetween(string $column, iterable $values) Add an or where between statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereBetweenColumns(string $column, array $values) Add an or where between statement using columns to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotBetween(string $column, iterable $values, string $boolean = 'and') Add a where not between statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotBetweenColumns(string $column, array $values, string $boolean = 'and') Add a where not between statement using columns to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotBetween(string $column, iterable $values) Add an or where not between statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotBetweenColumns(string $column, array $values) Add an or where not between statement using columns to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotNull(string $column) Add an "or where not null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this addDateBasedWhere(string $type, string $column, string $operator, mixed $value, string $boolean = 'and') Add a date based (year, month, day, time) statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNested(\Closure $callback, string $boolean = 'and') Add a nested where statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this addNestedWhereQuery(\Illuminate\Database\Eloquent\Builder $query, string $boolean = 'and') Add another query builder as a nested where to the query builder.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereSub(string $column, string $operator, \Closure $callback, string $boolean) Add a full sub-select to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereExists(\Closure $callback, string $boolean = 'and', bool $not = false) Add an exists clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereExists(\Closure $callback, bool $not = false) Add an or exists clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereNotExists(\Closure $callback, string $boolean = 'and') Add a where not exists clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereNotExists(\Closure $callback) Add a where not exists clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this addWhereExistsQuery(\Illuminate\Database\Eloquent\Builder $query, string $boolean = 'and', bool $not = false) Add an exists clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereRowValues(array $columns, string $operator, array $values, string $boolean = 'and') Adds a where condition using row values.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereRowValues(array $columns, string $operator, array $values) Adds an or where condition using row values.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereJsonContains(string $column, mixed $value, string $boolean = 'and', bool $not = false) Add a "where JSON contains" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereJsonContains(string $column, mixed $value) Add an "or where JSON contains" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereJsonDoesntContain(string $column, mixed $value, string $boolean = 'and') Add a "where JSON not contains" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereJsonDoesntContain(string $column, mixed $value) Add an "or where JSON not contains" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereJsonContainsKey(string $column, string $boolean = 'and', bool $not = false) Add a clause that determines if a JSON path exists to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereJsonContainsKey(string $column) Add an "or" clause that determines if a JSON path exists to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereJsonDoesntContainKey(string $column, string $boolean = 'and') Add a clause that determines if a JSON path does not exist to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereJsonDoesntContainKey(string $column) Add an "or" clause that determines if a JSON path does not exist to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereJsonLength(string $column, mixed $operator, mixed $value = null, string $boolean = 'and') Add a "where JSON length" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereJsonLength(string $column, mixed $operator, mixed $value = null) Add an "or where JSON length" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this dynamicWhere(string $method, array $parameters) Handles dynamic "where" clauses to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this whereFullText(string|string[] $columns, string $value, array $options = [], string $boolean = 'and') Add a "where fulltext" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orWhereFullText(string|string[] $columns, string $value, array $options = []) Add a "or where fulltext" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this groupBy(array|string ...$groups) Add a "group by" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this groupByRaw(string $sql, array $bindings = []) Add a raw groupBy clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this having(\Closure|string $column, string|null $operator = null, string|null $value = null, string $boolean = 'and') Add a "having" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orHaving(\Closure|string $column, string|null $operator = null, string|null $value = null) Add an "or having" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this havingNested(\Closure $callback, string $boolean = 'and') Add a nested having statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this addNestedHavingQuery(\Illuminate\Database\Eloquent\Builder $query, string $boolean = 'and') Add another query builder as a nested having to the query builder.
 * @method static \Illuminate\Database\Eloquent\Builder|$this havingNull(string|array $columns, string $boolean = 'and', bool $not = false) Add a "having null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orHavingNull(string $column) Add an "or having null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this havingNotNull(string|array $columns, string $boolean = 'and') Add a "having not null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orHavingNotNull(string $column) Add an "or having not null" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this doesntHave(string $column) Add an "doesnt Have" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this havingBetween(string $column, array $values, string $boolean = 'and', bool $not = false) Add a "having between " clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this havingRaw(string $sql, array $bindings = [], string $boolean = 'and') Add a raw having clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orHavingRaw(string $sql, array $bindings = []) Add a raw or having clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orderBy(\Closure|\Illuminate\Database\Eloquent\Builder|string $column, string $direction = 'asc') Add an "order by" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orderByDesc(\Closure|\Illuminate\Database\Eloquent\Builder|string $column) Add a descending "order by" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this latest(\Closure|\Illuminate\Database\Eloquent\Builder|string $column = 'created_at') Add an "order by" clause for a timestamp to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this oldest(\Closure|\Illuminate\Database\Eloquent\Builder|string $column = 'created_at') Add an "order by" clause for a timestamp to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this inRandomOrder(string $seed = '') Put the query's results in random order.
 * @method static \Illuminate\Database\Eloquent\Builder|$this orderByRaw(string $sql, array $bindings = []) Add a raw "order by" clause to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this skip(int $value) Alias to set the "offset" value of the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this offset(int $value) Set the "offset" value of the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this take(int $value) Alias to set the "limit" value of the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this limit(int $value) Set the "limit" value of the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this forPage(int $page, int $perPage = 15) Set the limit and offset for a given page.
 * @method static \Illuminate\Database\Eloquent\Builder|$this forPageBeforeId(int $perPage = 15, int|null $lastId = 0, string $column = 'id') Constrain the query to the previous "page" of results before a given ID.
 * @method static \Illuminate\Database\Eloquent\Builder|$this forPageAfterId(int $perPage = 15, int|null $lastId = 0, string $column = 'id') Constrain the query to the next "page" of results after a given ID.
 * @method static \Illuminate\Database\Eloquent\Builder|$this reorder(\Closure|\Illuminate\Database\Eloquent\Builder|string|null $column = null, string $direction = 'asc') Remove all existing orders and optionally add a new order.
 * @method static \Illuminate\Database\Eloquent\Builder|$this union(\Illuminate\Database\Eloquent\Builder|\Closure $query, bool $all = false) Add a union statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this unionAll(\Illuminate\Database\Eloquent\Builder|\Closure $query) Add a union all statement to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this lock(string|bool $value = true) Lock the selected rows in the table.
 * @method static \Illuminate\Database\Eloquent\Builder|$this beforeQuery(callable $callback) Register a closure to be invoked before the query is executed.
 * @method static \Illuminate\Database\Eloquent\Builder|$this setAggregate(string $function, array $columns) Set the aggregate property without running the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this setBindings(array $bindings, string $type = 'where') Set the bindings on the query builder.
 * @method static \Illuminate\Database\Eloquent\Builder|$this addBinding(mixed $value, string $type = 'where') Add a binding to the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this mergeBindings(\Illuminate\Database\Eloquent\Builder $query) Merge an array of bindings into our bindings.
 * @method static \Illuminate\Database\Eloquent\Builder|$this useWritePdo() Use the "write" PDO connection when executing the query.
 * @method static \Illuminate\Database\Eloquent\Builder|$this dump() Dump the current SQL and bindings.
 * @method static \Illuminate\Database\Eloquent\Builder upsert(array $values, array|string $uniqueBy, array|null $update = null) Insert new records or update the existing ones.
 * @method static \Illuminate\Database\Eloquent\Builder increment(string $column, float|int $amount = 1, array $extra = []) Increment a column's value by a given amount.
 * @method static \Illuminate\Database\Eloquent\Builder decrement(string $column, float|int $amount = 1, array $extra = []) Decrement a column's value by a given amount.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static find(mixed $id, array $columns = ['*']) Find a model by its primary key.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static findOrFail(mixed $id, array $columns = ['*']) Find a model by its primary key or throw an exception.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static findOrNew(mixed $id, array $columns = ['*']) Find a model by its primary key or return fresh model instance.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static firstOrNew(array $attributes = [], array $values = []) Get the first record matching the attributes or instantiate it.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static firstOrCreate(array $attributes = [], array $values = []) Get the first record matching the attributes or create it.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static create(array $data) Insert new records
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static updateOrCreate(array $attributes = [], array $values = []) Create or update a record matching the attributes, and fill it with values.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static firstOrFail(array $columns = ['*']) Execute the query and get the first result or throw an exception.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static firstOr(\Closure|array $columns = ['*'], \Closure $callback = null) Execute the query and get the first result or call a callback.
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|static getModels(array|string $columns = ['*']) Get the hydrated models without eager loading.
 * @method static \Illuminate\Database\Eloquent\Builder|static get (array|string $columns = ['*']) Execute the query as a "select" statement.
 */

class VnModelBase extends Model
{
    public const CREATED_AT = 'created', UPDATED_AT = 'updated';
    protected $dateFormat = 'U';
    protected $primaryKey = 'id';
    protected int $cacheTime = 0;

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        if($this->cacheTime > 0){
            return new QueryBuilderWithCache($connection, $connection->getQueryGrammar(), $connection->getPostProcessor(), $this->cacheTime);
        }
        return parent::newBaseQueryBuilder();
    }

    public function getCreatedDateAttribute(){
        return date('Y-m-d', $this->created->timestamp);
    }

    public function getUpdatedDateAttribute(){
        return date('Y-m-d', $this->updated->timestamp);
    }

}
