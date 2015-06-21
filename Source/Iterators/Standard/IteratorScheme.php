<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;
use Pinq\Iterators\Generators\IGenerator;

/**
 * Iterator scheme using the extended iterator API.
 * Compatible with PHP 5.4.0.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class IteratorScheme extends Common\IteratorScheme
{
    public static function compatibleWith($phpVersion)
    {
        return version_compare($phpVersion, '5.4.0', '>=');
    }

    public function createOrderedMap(\Traversable $iterator = null)
    {
        return new OrderedMap($iterator === null ? null : $this->toIterator($iterator));
    }

    public function createSet(\Traversable $iterator = null)
    {
        return new Set($iterator === null ? null : $this->toIterator($iterator));
    }

    public function walk(\Traversable $iterator, callable $function)
    {
        $iterator = $this->adapter($iterator);
        $iterator->rewind();

        while (($element = $iterator->fetch())
                && $function($element[1], $element[0]) !== false) {

        }
    }

    public function toArray(\Traversable $iterator)
    {
        $iterator = $this->arrayCompatibleIterator($this->adapter($iterator));
        $array    = [];

        $iterator->rewind();
        while ($element = $iterator->fetch()) {
            $array[$element[0]] =& $element[1];
        }

        return $array;
    }

    /**
     * @param \Traversable $iterator
     *
     * @return IIterator
     */
    public static function adapter(\Traversable $iterator)
    {
        if ($iterator instanceof IIterator) {
            return $iterator;
        } elseif ($iterator instanceof IGenerator) {
            return new IGeneratorAdapter($iterator);
        } elseif ($iterator instanceof \ArrayIterator) {
            return new ArrayIteratorAdapter($iterator);
        } elseif ($iterator instanceof \IteratorAggregate) {
            return static::adapter($iterator->getIterator());
        } else {
            return new IteratorAdapter($iterator);
        }
    }

    public function arrayCompatibleIterator(\Traversable $iterator)
    {
        $iterator = $this->adapter($iterator);
        if ($iterator->isArrayCompatible()) {
            return $iterator;
        }

        return new ArrayCompatibleIterator($this->adapter($iterator));
    }

    public function adapterIterator(\Traversable $iterator)
    {
        return $this->adapter($iterator);
    }

    public function arrayIterator(array $array)
    {
        return new ArrayIterator($array);
    }

    public function emptyIterator()
    {
        return new EmptyIterator();
    }

    public function filterIterator(\Traversable $iterator, callable $predicate)
    {
        return new FilterIterator($this->adapter($iterator), $predicate);
    }

    public function projectionIterator(
            \Traversable $iterator,
            callable $keyProjectionFunction = null,
            callable $valueProjectionFunction = null
    ) {
        return new ProjectionIterator(
                $this->adapter($iterator),
                $keyProjectionFunction,
                $valueProjectionFunction);
    }

    public function reindexerIterator(\Traversable $iterator)
    {
        return new ReindexedIterator($this->adapter($iterator));
    }

    public function rangeIterator(\Traversable $iterator, $start, $amount)
    {
        return new RangeIterator($this->adapter($iterator), $start, $amount);
    }

    public function groupedIterator(
            \Traversable $iterator,
            callable $groupKeyFunction,
            callable $traversableFactory
    ) {
        return new GroupedIterator(
                $this->adapter($iterator),
                $groupKeyFunction,
                $traversableFactory);
    }

    public function orderedIterator(\Traversable $iterator, callable $function, $isAscending)
    {
        return new OrderedIterator($this->adapter($iterator), $function, $isAscending);
    }

    public function joinIterator(\Traversable $outerIterator, \Traversable $innerIterator)
    {
        return new UnfilteredJoinIterator(
                $this->adapter($outerIterator),
                $this->adapter($innerIterator));
    }

    public function groupJoinIterator(
            \Traversable $outerIterator,
            \Traversable $innerIterator,
            callable $traversableFactory
    ) {
        return new UnfilteredGroupJoinIterator(
                $this->adapter($outerIterator),
                $this->adapter($innerIterator),
                $traversableFactory);
    }

    protected function setOperationIterator(\Traversable $iterator, Common\SetOperations\ISetFilter $setFilter)
    {
        return new SetOperationIterator($this->adapter($iterator), $setFilter);
    }

    protected function flattenedIteratorsIterator(\Traversable $iteratorsIterator)
    {
        return new FlatteningIterator($this->adapter($iteratorsIterator));
    }
}
