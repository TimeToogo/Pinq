<?php

namespace Pinq;

use Pinq\Iterators\IIteratorScheme;

/**
 * The standard traversable class, fully implements the traversable API
 * using iterators to achieve lazy evaluation
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Traversable implements ITraversable, Interfaces\IOrderedTraversable, \Serializable
{
    /**
     * The current iterator context for the traversable
     *
     * @var IIteratorScheme     
     */
    protected $scheme;
    
    /**
     * The current iterator for the traversable
     *
     * @var \Traversable
     */
    protected $valuesIterator;
    
    public function __construct($values = [], IIteratorScheme $scheme = null)
    {
        $this->scheme = $scheme ?: Iterators\SchemeProvider::getDefault();
        $this->valuesIterator = $this->scheme->toIterator($values);
    }

    /**
     * Constructs a new traversable object from the supplied values
     *
     * @param array|\Traversable $values The values
     * @param IIteratorScheme $scheme The iterator context
     * @return static
     */
    public static function from($values, IIteratorScheme $scheme = null)
    {
        return new static($values, $scheme);
    }

    /**
     * Returns a callable for the the traversable constructor
     *
     * @return callable
     */
    public static function factory(IIteratorScheme $scheme = null)
    {
        //static:: doesn't work in closures?
        $static = get_called_class();
        return function ($values) use ($static, $scheme) {
            return $static::from($values, $scheme);
        };
    }
    
    private function factoryWithScheme()
    {
        return static::factory($this->scheme);
    }

    private function newSelf($values)
    {
        return static::from($values, $this->scheme);
    }

    public function asArray()
    {
        return $this->scheme->toArray($this->toOrderedMap());
    }

    public function getIterator()
    {
        return $this->scheme->arrayCompatibleIterator($this->valuesIterator);
    }
    
    public function getTrueIterator()
    {
        return $this->valuesIterator;
    }
    
    public function getIteratorScheme()
    {
        return $this->scheme;
    }

    public function asTraversable()
    {
        return $this;
    }

    public function asCollection()
    {
        return new Collection($this->valuesIterator, $this->scheme);
    }
    
    /**
     * @return Iterators\IOrderedMap
     */
    final protected function toOrderedMap()
    {
        if(!($this->valuesIterator instanceof Iterators\IOrderedMap)) {
            $this->valuesIterator = $this->scheme->createOrderedMap($this->valuesIterator);
        }
        
        return $this->valuesIterator;
    }
    
    public function iterate(callable $function)
    {
        $this->scheme->walk($this->valuesIterator, $function);
    }

    public function serialize()
    {
        return serialize([$this->scheme, $this->toOrderedMap()]);
    }

    public function unserialize($serialized)
    {
        list($this->scheme, $this->valuesIterator) = unserialize($serialized);
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
        $value = null;
        
        foreach($this->toOrderedMap() as $value) {
            
        }
        
        return $value;
    }

    public function where(callable $predicate)
    {
        return $this->newSelf($this->scheme->filterIterator($this->valuesIterator, $predicate));
    }

    public function orderByAscending(callable $function)
    {
        return $this->newSelf($this->scheme->orderedIterator($this->valuesIterator, $function, true));
    }

    public function orderByDescending(callable $function)
    {
        return $this->newSelf($this->scheme->orderedIterator($this->valuesIterator, $function, false));
    }

    public function orderBy(callable $function, $direction)
    {
        return $direction === Direction::DESCENDING ? $this->orderByDescending($function) : $this->orderByAscending($function);
    }
    
    /**
     * Verifies that the traversable is ordered.
     * 
     * @param string $method The called method name
     * @return Iterators\IOrderedIterator
     * @throws PinqException
     */
    private function validateIsOrdered($method)
    {
        $innerIterator = $this->valuesIterator;
        if (!($innerIterator instanceof Iterators\IOrderedIterator)) {
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
        return $this->newSelf($this->validateIsOrdered(__METHOD__)->thenOrderBy(
                $function,
                $direction !== Direction::DESCENDING));
    }

    public function thenByAscending(callable $function)
    {
        return $this->newSelf($this->validateIsOrdered(__METHOD__)->thenOrderBy($function, true));
    }

    public function thenByDescending(callable $function)
    {
        return $this->newSelf($this->validateIsOrdered(__METHOD__)->thenOrderBy($function, false));
    }

    public function skip($amount)
    {
        return $this->newSelf($this->scheme->rangeIterator($this->valuesIterator, $amount, null));
    }

    public function take($amount)
    {
        return $this->newSelf($this->scheme->rangeIterator($this->valuesIterator, 0, $amount));
    }

    public function slice($start, $amount)
    {
        return $this->newSelf($this->scheme->rangeIterator($this->valuesIterator, $start, $amount));
    }

    public function indexBy(callable $function)
    {
        return $this->newSelf($this->scheme->uniqueKeyIterator(
                $this->scheme->projectionIterator(
                        $this->valuesIterator,
                        $function,
                        null)));
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
        return $this->newSelf($this->scheme->projectionIterator(
                $this->valuesIterator, 
                $this->reindexer(), 
                function ($value, $key) {
                    return $key;
                }));
    }
    
    public function reindex()
    {
        return $this->newSelf($this->scheme->projectionIterator(
                $this->valuesIterator, 
                $this->reindexer(), 
                null));
    }

    public function groupBy(callable $function)
    {
        return $this->newSelf($this->scheme->groupedIterator(
                $this->valuesIterator, 
                $function, 
                $this->factoryWithScheme()));
    }

    public function join($values)
    {
        return new Connectors\JoiningOnTraversable(
                $this->scheme,
                $this->valuesIterator,
                $this->scheme->toIterator($values),
                false,
                $this->factoryWithScheme());
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningOnTraversable(
                $this->scheme,
                $this->valuesIterator,
                $this->scheme->toIterator($values),
                true,
                $this->factoryWithScheme());
    }

    public function unique()
    {
        return $this->newSelf($this->scheme->uniqueIterator($this->valuesIterator));
    }

    public function select(callable $function)
    {
        return $this->newSelf($this->scheme->projectionIterator(
                $this->valuesIterator,
                null,
                $function));
    }

    public function selectMany(callable $function)
    {
        $projectionIterator =
                $this->scheme->projectionIterator(
                        $this->valuesIterator,
                        null,
                        $function);

        return $this->newSelf($this->scheme->flattenedIterator($projectionIterator));
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Set Operations">

    public function union($values)
    {
        return $this->newSelf($this->scheme->unionIterator(
                $this->valuesIterator,
                $this->scheme->toIterator($values)));
    }

    public function intersect($values)
    {
        return $this->newSelf($this->scheme->intersectionIterator(
                $this->valuesIterator,
                $this->scheme->toIterator($values)));
    }

    public function difference($values)
    {
        return $this->newSelf($this->scheme->differenceIterator(
                $this->valuesIterator,
                $this->scheme->toIterator($values)));
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Multiset Operations">

    public function append($values)
    {
        return $this->newSelf($this->scheme->appendIterator(
                    $this->valuesIterator, 
                    $this->scheme->toIterator($values)));
    }

    public function whereIn($values)
    {
        return $this->newSelf($this->scheme->whereInIterator(
                $this->valuesIterator,
                $this->scheme->toIterator($values)));
    }

    public function except($values)
    {
        return $this->newSelf($this->scheme->exceptIterator(
                $this->valuesIterator,
                $this->scheme->toIterator($values)));
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
            return $this->scheme->toArray($this->scheme->projectionIterator(
                    $this->toOrderedMap(), 
                    null,
                    $function));
        }
    }

    private function mapIterator(callable $function = null)
    {
        if ($function === null) {
            return $this->valuesIterator;
        } else {
            return $this->scheme->projectionIterator(
                    $this->valuesIterator, 
                    $function,
                    null);
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
        foreach ($this->mapIterator($function) as $value) {
            if (!$value) {
                return false;
            }
        }

        return true;
    }

    public function any(callable $function = null)
    {
        foreach ($this->mapIterator($function) as $value) {
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
