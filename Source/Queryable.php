<?php

namespace Pinq;

use Pinq\Queries;
use Pinq\Queries\Requests;
use Pinq\Queries\Segments;
use Pinq\Iterators\IIteratorScheme;

/**
 * The standard queryable class, fully implements the queryable API
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Queryable implements IQueryable, Interfaces\IOrderedQueryable
{
    /**
     * The iterator context for the queryable
     *
     * @var IIteratorScheme     
     */
    protected $scheme;
    
    /**
     * The query provider implementation for this queryable
     *
     * @var Providers\IQueryProvider
     */
    protected $provider;

    /**
     * The function converter from the query provider
     *
     * @var Parsing\IFunctionToExpressionTreeConverter
     */
    protected $functionConverter;

    /**
     * The query scope of this instance
     *
     * @var Queries\IScope
     */
    protected $scope;

    /**
     * The underlying values iterator if loaded
     *
     * @var \Iterator|null
     */
    protected $valuesIterator = null;

    public function __construct(Providers\IQueryProvider $provider, Queries\IScope $scope = null, IIteratorScheme $scheme = null)
    {
        $this->provider = $provider;
        $this->functionConverter = $provider->getFunctionToExpressionTreeConverter();
        $this->scope = $scope ?: new Queries\Scope([]);
        $this->scheme = $scheme ?: Iterators\SchemeProvider::getDefault();
    }

    /**
     * Returns a new queryable instance with the supplied query segment
     * appended to the current scope
     *
     * @param Queries\ISegment $segment The new segment
     * @return IQueryable
     */
    protected function newSegment(Queries\ISegment $segment)
    {
        return $this->provider->createQueryable($this->scope->append($segment));
    }

    /**
     * Returns a new queryable instance with the supplied query segment
     * updating the last segment of the current scope
     *
     * @param Queries\ISegment $segment The new segment
     * @return IQueryable
     */
    protected function updateLastSegment(Queries\ISegment $segment)
    {
        return $this->provider->createQueryable($this->scope->updateLast($segment));
    }

    /**
     * Returns the requested query from the query provider.
     *
     * @param Queries\IRequest $request The request to load
     * @return mixed The result of the request query
     */
    private function loadQuery(Queries\IRequest $request)
    {
        return $this->provider->load(new Queries\RequestQuery($this->scope, $request));
    }

    /**
     * Loads the values iterator if not already load
     *
     * @return void
     */
    private function load()
    {
        if ($this->valuesIterator === null) {
            $this->valuesIterator = $this->scheme->toIterator($this->loadQuery(new Requests\Values()));
        }
    }
    
    public function isSource()
    {
        return $this->scope->isEmpty();
    }
    
    public function getSource()
    {
        return $this->isSource() ? $this : $this->provider->createQueryable();
    }

    final public function asArray()
    {
        $this->load();
        
        return $this->scheme->toArray($this->valuesIterator);
    }

    final public function getIterator()
    {
        $this->load();

        return $this->scheme->arrayCompatibleIterator($this->valuesIterator);
    }
    
    public function getTrueIterator()
    {
        $this->load();
        
        return $this->valuesIterator;
    }
    
    public function getIteratorScheme()
    {
        return $this->scheme;
    }

    public function asTraversable()
    {
        $this->load();

        return new Traversable($this->valuesIterator);
    }
    
    public function asCollection()
    {
        $this->load();
        
        return new Collection($this->valuesIterator);
    }
    
    public function iterate(callable $function)
    {
        $this->load();
        
        $this->scheme->walk($this->valuesIterator, $function);
    }

    final public function getProvider()
    {
        return $this->provider;
    }

    public function getScope()
    {
        return $this->scope;
    }

    final protected function convert(callable $function = null)
    {
        return $function === null ? null : $this->functionConverter->convert($function);
    }

    // <editor-fold defaultstate="collapsed" desc="Query segments">

    public function select(callable $function)
    {
        return $this->newSegment(new Segments\Select($this->convert($function)));
    }

    public function selectMany(callable $function)
    {
        return $this->newSegment(new Segments\SelectMany($this->convert($function)));
    }

    public function indexBy(callable $function)
    {
        return $this->newSegment(new Segments\IndexBy($this->convert($function)));
    }
    
    public function keys()
    {
        return $this->newSegment(new Segments\Keys());
    }
    
    public function reindex()
    {
        return $this->newSegment(new Segments\Reindex());
    }

    public function where(callable $predicate)
    {
        return $this->newSegment(new Segments\Filter($this->convert($predicate)));
    }

    public function groupBy(callable $function)
    {
        return $this->newSegment(new Segments\GroupBy([$this->convert($function)]));
    }

    public function join($values)
    {
        return new Connectors\JoiningQueryable($this->provider, $this->scope, $values, false);
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningQueryable($this->provider, $this->scope, $values, true);
    }

    public function union($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::UNION, $values));
    }

    public function intersect($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::INTERSECT, $values));
    }

    public function difference($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::DIFFERENCE, $values));
    }

    public function append($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::APPEND, $values));
    }

    public function whereIn($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::WHERE_IN, $values));
    }

    public function except($values)
    {
        return $this->newSegment(new Segments\Operation(Segments\Operation::EXCEPT, $values));
    }

    public function skip($amount)
    {
        return $this->newSegment(new Segments\Range($amount, null));
    }

    public function take($amount)
    {
        return $this->newSegment(new Segments\Range(0, $amount));
    }

    public function slice($start, $amount)
    {
        return $this->newSegment(new Segments\Range($start, $amount));
    }

    public function orderBy(callable $function, $direction)
    {
        return $this->newSegment(new Segments\OrderBy(
                [new Segments\OrderFunction(
                        $this->convert($function), 
                        $direction !== Direction::DESCENDING)]));
    }

    public function orderByAscending(callable $function)
    {
        return $this->newSegment(new Segments\OrderBy(
                [new Segments\OrderFunction(
                            $this->convert($function), 
                            true)]));
    }

    public function orderByDescending(callable $function)
    {
        return $this->newSegment(new Segments\OrderBy(
                [new Segments\OrderFunction(
                            $this->convert($function), 
                            false)]));
    }

    private function validateOrderBy($method)
    {
        $segments = $this->scope->getSegments();
        $lastSegment = end($segments);

        if (!$lastSegment instanceof Segments\OrderBy) {
            throw new PinqException(
                    'Invalid call to %s: %s::%s must be called first',
                    $method,
                    __CLASS__,
                    'orderBy');
        }

        return $lastSegment;
    }

    public function thenBy(callable $function, $direction)
    {
        return $this->updateLastSegment($this->validateOrderBy(__METHOD__)->thenBy(
                $this->convert($function),
                $direction !== Direction::DESCENDING));
    }

    public function thenByAscending(callable $function)
    {
        return $this->updateLastSegment($this->validateOrderBy(__METHOD__)
                ->thenBy($this->convert($function), true));
    }

    public function thenByDescending(callable $function)
    {
        return $this->updateLastSegment($this->validateOrderBy(__METHOD__)
                ->thenBy($this->convert($function), false));
    }

    public function unique()
    {
        return $this->newSegment(new Segments\Unique());
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Query Requests">

    public function offsetExists($index)
    {
        return $this->loadQuery(new Requests\IssetIndex($index));
    }

    public function offsetGet($index)
    {
        return $this->loadQuery(new Requests\GetIndex($index));
    }

    public function offsetSet($index, $value)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function offsetUnset($index)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function first()
    {
        return $this->loadQuery(new Requests\First());
    }

    public function last()
    {
        return $this->loadQuery(new Requests\Last());
    }

    public function count()
    {
        return $this->loadQuery(new Requests\Count());
    }

    public function isEmpty()
    {
        return $this->loadQuery(new Requests\IsEmpty());
    }

    public function contains($value)
    {
        return $this->loadQuery(new Requests\Contains($value));
    }

    public function aggregate(callable $function)
    {
        return $this->loadQuery(new Requests\Aggregate($this->convert($function)));
    }

    public function all(callable $function = null)
    {
        return $this->loadQuery(new Requests\All($this->convert($function)));
    }

    public function any(callable $function = null)
    {
        return $this->loadQuery(new Requests\Any($this->convert($function)));
    }

    public function maximum(callable $function = null)
    {
        return $this->loadQuery(new Requests\Maximum($this->convert($function)));
    }

    public function minimum(callable $function = null)
    {
        return $this->loadQuery(new Requests\Minimum($this->convert($function)));
    }

    public function sum(callable $function = null)
    {
        return $this->loadQuery(new Requests\Sum($this->convert($function)));
    }

    public function average(callable $function = null)
    {
        return $this->loadQuery(new Requests\Average($this->convert($function)));
    }

    public function implode($delimiter, callable $function = null)
    {
        return $this->loadQuery(new Requests\Implode($delimiter, $this->convert($function)));
    }

    // </editor-fold>
}
