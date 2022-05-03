<?php

namespace Pinq;

use Pinq\Expressions as O;
use Pinq\Iterators\IIteratorScheme;
use Pinq\Queries\Requests;
use Pinq\Queries\Segments;

/**
 * The standard queryable class, fully implements the queryable API
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Queryable extends QueryBuilder implements IQueryable, Interfaces\IOrderedQueryable
{
    /**
     * @var Queries\ISourceInfo
     */
    protected $sourceInfo;

    /**
     * @var IIteratorScheme
     */
    protected $scheme;

    public function __construct(
            Providers\IQueryProvider $provider,
            Queries\ISourceInfo $sourceInfo,
            O\TraversalExpression $queryExpression = null,
            IIteratorScheme $scheme = null
    ) {
        parent::__construct($provider);
        $this->sourceInfo = $sourceInfo;
        $this->expression = $queryExpression ?: O\Expression::value($this);
        $this->scheme     = $scheme ?: Iterators\SchemeProvider::getDefault();
    }

    /**
     * Returns the requested query from the query provider.
     *
     * @param O\Expression $expression
     *
     * @return mixed The result of the request query
     */
    final protected function loadQuery(O\Expression $expression)
    {
        return $this->provider->load($expression);
    }

    public function getExpression()
    {
        return $this->expression;
    }

    public function getSourceInfo()
    {
        return $this->sourceInfo;
    }

    public function isSource()
    {
        return $this->expression instanceof O\ValueExpression && $this->expression->getValue() === $this;
    }

    public function getSource()
    {
        if ($this->isSource()) {
            return $this;
        } else {
            $expression = $this->expression;
            while ($expression instanceof O\TraversalExpression) {
                $expression = $expression->getValue();
            }

            if ($expression instanceof O\ValueExpression) {
                return $expression->getValue();
            } else {
                throw new PinqException(
                        'Invalid origin expression: must be instance of %s',
                        O\ValueExpression::getType());
            }
        }
    }

    final public function asArray()
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    final public function getIterator(): \Traversable
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function getTrueIterator()
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function asTraversable()
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function asCollection()
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function getIteratorScheme()
    {
        return $this->scheme;
    }

    public function iterate(callable $function)
    {
        $this->scheme->walk($this->getTrueIterator(), $function);
    }

    final public function getProvider()
    {
        return $this->provider;
    }

    // <editor-fold defaultstate="collapsed" desc="Query segments">

    public function select(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function selectMany(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function indexBy(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function keys()
    {
        return $this->newMethodSegment(__FUNCTION__);
    }

    public function reindex()
    {
        return $this->newMethodSegment(__FUNCTION__);
    }

    public function where(callable $predicate)
    {
        return $this->newMethodSegment(__FUNCTION__, [$predicate]);
    }

    public function groupBy(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function join($values)
    {
        return new Connectors\JoiningQueryable($this->provider, $this->newMethod(__FUNCTION__, [$values]));
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningQueryable($this->provider, $this->newMethod(__FUNCTION__, [$values]));
    }

    public function union($values)
    {
        return $this->newMethodSegment(__FUNCTION__, [$values]);
    }

    public function intersect($values)
    {
        return $this->newMethodSegment(__FUNCTION__, [$values]);
    }

    public function difference($values)
    {
        return $this->newMethodSegment(__FUNCTION__, [$values]);
    }

    public function append($values)
    {
        return $this->newMethodSegment(__FUNCTION__, [$values]);
    }

    public function whereIn($values)
    {
        return $this->newMethodSegment(__FUNCTION__, [$values]);
    }

    public function except($values)
    {
        return $this->newMethodSegment(__FUNCTION__, [$values]);
    }

    public function skip($amount)
    {
        return $this->newMethodSegment(__FUNCTION__, [$amount]);
    }

    public function take($amount)
    {
        return $this->newMethodSegment(__FUNCTION__, [$amount]);
    }

    public function slice($start, $amount)
    {
        return $this->newMethodSegment(__FUNCTION__, [$start, $amount]);
    }

    public function orderBy(callable $function, $direction)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function, $direction]);
    }

    public function orderByAscending(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function orderByDescending(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function thenBy(callable $function, $direction)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function, $direction]);
    }

    public function thenByAscending(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function thenByDescending(callable $function)
    {
        return $this->newMethodSegment(__FUNCTION__, [$function]);
    }

    public function unique()
    {
        return $this->newMethodSegment(__FUNCTION__);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Query Requests">

    public function offsetExists($index): bool
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$index]));
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($index)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$index]));
    }

    public function offsetSet($index, $value): void
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function offsetUnset($index): void
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function first()
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function last()
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function count(): int
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function isEmpty()
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__));
    }

    public function contains($value)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$value]));
    }

    public function aggregate(callable $function)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function all(callable $function = null)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function any(callable $function = null)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function maximum(callable $function = null)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function minimum(callable $function = null)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function sum(callable $function = null)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function average(callable $function = null)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$function]));
    }

    public function implode($delimiter, callable $function = null)
    {
        return $this->loadQuery($this->newMethod(__FUNCTION__, [$delimiter, $function]));
    }

    // </editor-fold>
}
