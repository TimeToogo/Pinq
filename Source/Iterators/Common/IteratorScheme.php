<?php

namespace Pinq\Iterators\Common;

use Pinq\Iterators\IIteratorScheme;
use Pinq\PinqException;

/**
 * Supplies common implementation for an iterator scheme
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class IteratorScheme implements IIteratorScheme
{
    public function toIterator($traversableOrArray)
    {
        $isArray = is_array($traversableOrArray);
        if (!$isArray && !($traversableOrArray instanceof \Traversable)) {
            throw PinqException::invalidIterable(__METHOD__, $traversableOrArray);
        }

        if ($isArray) {
            return $this->arrayIterator($traversableOrArray);
        } else {
            return $this->adapterIterator($traversableOrArray);
        }
    }

    /**
     * @param \Traversable $iterator
     *
     * @return \Traversable
     */
    abstract protected function adapterIterator(\Traversable $iterator);

    final public function flattenedIterator(\Traversable $iterator)
    {
        return $this->flattenedIteratorsIterator(
                $this->projectionIterator(
                        $iterator,
                        null,
                        [$this, 'toIterator']
                )
        );
    }

    abstract protected function flattenedIteratorsIterator(\Traversable $iteratorsIterator);

    final public function appendIterator(\Traversable $iterator, \Traversable $otherIterator)
    {
        return $this->flattenedIterator($this->arrayIterator([$iterator, $otherIterator]));
    }

    final public function unionIterator(\Traversable $iterator, \Traversable $otherIterator)
    {
        return $this->reindexerIterator($this->uniqueIterator($this->appendIterator($iterator, $otherIterator)));
    }

    final public function uniqueIterator(\Traversable $iterator)
    {
        return $this->setOperationIterator(
                $iterator,
                new SetOperations\UniqueFilter($this)
        );
    }

    final public function uniqueKeyIterator(\Traversable $iterator)
    {
        return $this->setOperationIterator(
                $iterator,
                new SetOperations\UniqueKeyFilter($this)
        );
    }

    final public function whereInIterator(\Traversable $iterator, \Traversable $otherIterator)
    {
        return $this->setOperationIterator(
                $iterator,
                new SetOperations\WhereInFilter($this, $otherIterator)
        );
    }

    final public function exceptIterator(\Traversable $iterator, \Traversable $otherIterator)
    {
        return $this->setOperationIterator(
                $iterator,
                new SetOperations\ExceptFilter($this, $otherIterator)
        );
    }

    final public function intersectionIterator(\Traversable $iterator, \Traversable $otherIterator)
    {
        return $this->setOperationIterator(
                $iterator,
                new SetOperations\IntersectionFilter($this, $otherIterator)
        );
    }

    final public function differenceIterator(\Traversable $iterator, \Traversable $otherIterator)
    {
        return $this->setOperationIterator(
                $iterator,
                new SetOperations\DifferenceFilter($this, $otherIterator)
        );
    }

    abstract protected function setOperationIterator(\Traversable $iterator, SetOperations\ISetFilter $setFilter);
}
