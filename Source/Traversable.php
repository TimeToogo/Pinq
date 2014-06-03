<?php

namespace Pinq;

/**
 * The standard traversable class, fully implements the traversable API
 * using iterators to achieve lazy evaluation
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Traversable implements ITraversable, Interfaces\IOrderedTraversable, \Serializable
{
    /**
     * The current iterator for the traversable
     *
     * @var IIterator
     */
    protected $valuesIterator;
    
    public function __construct($values = [])
    {
        $this->valuesIterator = Utilities::toIterator($values);
    }

    /**
     * Constructs a new traversable object from the supplied values
     *
     * @param array|\Traversable $values The values
     * @return static
     */
    public static function from($values)
    {
        return new static($values);
    }

    /**
     * Returns a callable for the the traversable constructor
     *
     * @return callable
     */
    public static function factory()
    {
        return [get_called_class(), 'from'];
    }

    final public function getIterator()
    {
        return new Iterators\ArrayCompatibleIterator($this->valuesIterator);
    }

    public function asArray()
    {
        return Utilities::toArray($this->toOrderedMap());
    }
    
    public function getTrueIterator()
    {
        return $this->valuesIterator;
    }

    public function asTraversable()
    {
        return $this;
    }

    public function asCollection()
    {
        return new Collection($this->valuesIterator);
    }
    
    /**
     * @return Iterators\Utilities\OrderedMap
     */
    final protected function toOrderedMap()
    {
        if(!($this->valuesIterator instanceof Iterators\Utilities\OrderedMap)) {
            $this->valuesIterator = new Iterators\Utilities\OrderedMap($this->valuesIterator);
        }
        
        return $this->valuesIterator;
    }
    
    public function iterate(callable $function)
    {
        Utilities::iteratorWalk($this->valuesIterator, $function);
    }

    public function serialize()
    {
        return serialize($this->toOrderedMap());
    }

    public function unserialize($serialized)
    {
        $this->valuesIterator = unserialize($serialized);
    }

    // <editor-fold defaultstate="collapsed" desc="Querying">

    public function first()
    {
        foreach ($this->valuesIterator as $value) {
            return $value;
        }

        return null;
    }

    public function last()
    {
        $array = $this->asArray();

        return end($array) ?: null;
    }

    public function where(callable $predicate)
    {
        return static::from(new Iterators\FilterIterator($this->valuesIterator, $predicate));
    }

    public function orderByAscending(callable $function)
    {
        return static::from(new Iterators\OrderedIterator($this->valuesIterator, $function, true));
    }

    public function orderByDescending(callable $function)
    {
        return static::from(new Iterators\OrderedIterator($this->valuesIterator, $function, false));
    }

    public function orderBy(callable $function, $direction)
    {
        return $direction === Direction::DESCENDING ? $this->orderByDescending($function) : $this->orderByAscending($function);
    }
    
    /**
     * Verifies that the traversable is ordered.
     * 
     * @param string $method The called method name
     * @return Iterators\OrderedIterator
     * @throws PinqException
     */
    private function validateIsOrdered($method)
    {
        $innerIterator = $this->valuesIterator;
        if (!($innerIterator instanceof Iterators\OrderedIterator)) {
            throw new PinqException(
                    'Invalid call to %s: %s::%s must be called first.',
                    $method,
                    __CLASS__,
                    'orderBy');
        }

        return $innerIterator;
    }
    
    public function thenBy(callable $function, $direction)
    {
        return static::from($this->validateIsOrdered(__METHOD__)->thenOrderBy(
                $function,
                $direction !== Direction::DESCENDING));
    }

    public function thenByAscending(callable $function)
    {
        return static::from($this->validateIsOrdered(__METHOD__)->thenOrderBy($function, true));
    }

    public function thenByDescending(callable $function)
    {
        return static::from($this->validateIsOrdered(__METHOD__)->thenOrderBy($function, false));
    }

    public function skip($amount)
    {
        return static::from(new Iterators\RangeIterator($this->valuesIterator, $amount, null));
    }

    public function take($amount)
    {
        return static::from(new Iterators\RangeIterator($this->valuesIterator, 0, $amount));
    }

    public function slice($start, $amount)
    {
        return static::from(new Iterators\RangeIterator($this->valuesIterator, $start, $amount));
    }

    public function indexBy(callable $function)
    {
        return static::from(new Iterators\ProjectionIterator(
                $this->valuesIterator,
                $function,
                null));
    }
    
    private function reindexer()
    {
        $count = 0;
        return function () use (&$count) {
            return $count++;
        };
    }
    
    public function keys()
    {
        return static::from(new Iterators\ProjectionIterator(
                $this->valuesIterator, 
                $this->reindexer(), 
                function ($value, $key) {
                    return $key;
                }));
    }
    
    public function reindex()
    {
        return static::from(new Iterators\ProjectionIterator(
                $this->valuesIterator, 
                $this->reindexer(), 
                null));
    }

    public function groupBy(callable $function)
    {
        return static::from(new Iterators\GroupedIterator($this->valuesIterator, $function, static::factory()));
    }

    public function join($values)
    {
        return new Connectors\JoiningOnTraversable(
                $this->valuesIterator,
                Utilities::toIterator($values),
                false,
                static::factory());
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningOnTraversable(
                $this->valuesIterator,
                Utilities::toIterator($values),
                true,
                static::factory());
    }

    public function unique()
    {
        return static::from(new Iterators\UniqueIterator($this->valuesIterator));
    }

    public function select(callable $function)
    {
        return static::from(new Iterators\ProjectionIterator(
                $this->valuesIterator,
                null,
                $function));
    }

    public function selectMany(callable $function)
    {
        $projectionIterator =
                new Iterators\ProjectionIterator(
                        $this->valuesIterator,
                        null,
                        $function);

        return static::from(new Iterators\FlatteningIterator($projectionIterator));
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Set Operations">

    public function union($values)
    {
        return static::from(new Iterators\UnionIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }

    public function intersect($values)
    {
        return static::from(new Iterators\IntersectionIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }

    public function difference($values)
    {
        return static::from(new Iterators\DifferenceIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Multiset Operations">

    public function append($values)
    {
        return static::from(new Iterators\FlatteningIterator(
                new Iterators\ArrayIterator([
                    $this->valuesIterator, 
                    Utilities::toIterator($values)])));
    }

    public function whereIn($values)
    {
        return static::from(new Iterators\WhereInIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }

    public function except($values)
    {
        return static::from(new Iterators\ExceptIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Array Access">

    public function offsetExists($index)
    {
        return $this->toOrderedMap()->offsetExists($index);
    }

    public function offsetGet($index)
    {
        return $this->toOrderedMap()->offsetGet($index);
    }

    public function offsetSet($index, $value)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function offsetUnset($index)
    {
        throw PinqException::notSupported(__METHOD__);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Aggregates">

    public function count()
    {
        return $this->valuesIterator instanceof \Countable ? 
                $this->valuesIterator->count() : $this->toOrderedMap()->count();
    }

    public function exists()
    {
        foreach ($this->valuesIterator as $value) {
            return true;
        }

        return false;
    }

    public function contains($value)
    {
        foreach ($this->valuesIterator as $containedValue) {
            if ($containedValue === $value) {
                return true;
            }
        }

        return false;
    }

    public function aggregate(callable $function)
    {
        $hasValue = false;
        $aggregateValue = null;

        foreach ($this->asArray() as $value) {
            if (!$hasValue) {
                $aggregateValue = $value;
                $hasValue = true;
                continue;
            }

            $aggregateValue = $function($aggregateValue, $value);
        }

        return $aggregateValue;
    }

    private function mapArray(callable $function = null)
    {
        if ($function === null) {
            return $this->asArray();
        } else {
            $mappedArray = [];
            $function = Iterators\Utilities\Functions::allowExcessiveArguments($function);
            
            $this->iterate(function ($value, $key) use($function, &$mappedArray) {
                $mappedArray[] = $function($value, $key);
            });
            
            return $mappedArray;
        }
    }

    public function maximum(callable $function = null)
    {
        $array = $this->mapArray($function);

        return empty($array) ? null : max($array);
    }

    public function minimum(callable $function = null)
    {
        $array = $this->mapArray($function);

        return empty($array) ? null : min($array);
    }

    public function sum(callable $function = null)
    {
        $array = $this->mapArray($function);

        return empty($array) ? null : array_sum($array);
    }

    public function average(callable $function = null)
    {
        $array = $this->mapArray($function);

        return empty($array) ? null : array_sum($array) / count($array);
    }

    public function all(callable $function = null)
    {
        foreach ($this->mapArray($function) as $value) {
            if (!$value) {
                return false;
            }
        }

        return true;
    }

    public function any(callable $function = null)
    {
        foreach ($this->mapArray($function) as $value) {
            if ($value) {
                return true;
            }
        }

        return false;
    }

    public function implode($delimiter, callable $function = null)
    {
        return implode($delimiter, $this->mapArray($function));
    }

    // </editor-fold>
}
