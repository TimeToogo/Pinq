<?php

namespace Pinq\Iterators\Generators;

use Pinq\Iterators\Common;
use Pinq\Iterators\Standard\IIterator;

/**
 * Iterator scheme using rewindable generator implementations.
 * Compatible with >= PHP 5.5.0.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class GeneratorScheme extends Common\IteratorScheme
{
    public static function compatibleWith($phpVersion)
    {
        // HHVM does not support foreach by reference on iterators.
        // This is used extensively by the generator classes,
        // hence fallback to the standard iterator scheme.
        return version_compare($phpVersion, '5.5.0', '>=')
                && strpos($phpVersion, 'hhvm') === false
                && strpos($phpVersion, 'hiphop') === false;
    }

    public function createOrderedMap(\Traversable $iterator = null)
    {
        return new OrderedMap($iterator === null ? null : $this->adapter($iterator));
    }

    public function createSet(\Traversable $iterator = null)
    {
        return new Set($iterator === null ? null : $this->adapter($iterator));
    }

    public function walk(\Traversable $iterator, callable $function)
    {
        $adapter = $this->adapter($iterator);
        foreach ($adapter as $key => &$value) {
            if ($function($value, $key) === false) {
                break;
            }
        }
    }

    public function toArray(\Traversable $iterator)
    {
        $iterator = $this->arrayCompatibleIterator($iterator);
        $array    = [];

        foreach ($iterator as $key => &$value) {
            $array[$key] =& $value;
        }

        return $array;
    }

    public function arrayCompatibleIterator(\Traversable $iterator)
    {
        $iterator = $this->adapter($iterator);
        if ($iterator->isArrayCompatible()) {
            return $iterator;
        }

        return new ArrayCompatibleIterator($this->adapter($iterator));
    }

    /**
     * @param \Traversable $iterator
     *
     * @return IGenerator
     */
    public static function adapter(\Traversable $iterator)
    {
        if ($iterator instanceof IGenerator) {
            return $iterator;
        } elseif ($iterator instanceof IIterator) {
            return new IIteratorAdapter($iterator);
        } elseif ($iterator instanceof \ArrayIterator) {
            return new ArrayIteratorAdapter($iterator);
        } elseif ($iterator instanceof \IteratorAggregate) {
            return static::adapter($iterator->getIterator());
        } else {
            return new IteratorAdapter($iterator);
        }
    }

    protected function adapterIterator(\Traversable $iterator)
    {
        return static::adapter($iterator);
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
        return new OrderedIterator(
                $this->adapter($iterator),
                $function,
                $isAscending);
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
