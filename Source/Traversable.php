<?php 

namespace Pinq;

/**
 * The standard traversable class, fully implements the traversable API
 * using iterator to achieve lazy evaluation
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Traversable implements \Pinq\ITraversable, \Serializable
{
    /**
     * The current iterator for the traversable
     * 
     * @var \Iterator 
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
    
    public final function getIterator()
    {
        return $this->valuesIterator;
    }
    
    public function asArray()
    {
        $array = Utilities::toArray($this->valuesIterator);
        $this->valuesIterator = new \ArrayIterator($array);
        
        return $array;
    }
    
    public function asTraversable()
    {
        return $this;
    }
    
    public function asCollection()
    {
        return new Collection($this->valuesIterator);
    }
    
    public function asQueryable()
    {
        return (new Providers\Traversable\Provider($this))->createQueryable();
    }
    
    public function asRepository()
    {
        return (new Collection($this->valuesIterator))->asRepository();
    }
    
    public function serialize()
    {
        return serialize($this->asArray());
    }
    
    public function unserialize($serialized)
    {
        $this->valuesIterator = new \ArrayIterator(unserialize($serialized));
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
        return new self(new Iterators\FilterIterator($this->valuesIterator, $predicate));
    }
    
    public function orderByAscending(callable $function)
    {
        return new OrderedTraversable(new Iterators\OrderedIterator($this->valuesIterator, $function, true));
    }
    
    public function orderByDescending(callable $function)
    {
        return new OrderedTraversable(new Iterators\OrderedIterator($this->valuesIterator, $function, false));
    }
    
    public function orderBy(callable $function, $direction)
    {
        return $direction === Direction::DESCENDING ? $this->orderByDescending($function) : $this->orderByAscending($function);
    }
    
    public function skip($amount)
    {
        return new self(new Iterators\RangeIterator($this->valuesIterator, $amount, null));
    }
    
    public function take($amount)
    {
        return new self(new Iterators\RangeIterator($this->valuesIterator, 0, $amount));
    }
    
    public function slice($start, $amount)
    {
        return new self(new Iterators\RangeIterator($this->valuesIterator, $start, $amount));
    }
    
    public function indexBy(callable $function)
    {
        return new self(new Iterators\ProjectionIterator(
                $this->valuesIterator,
                $function,
                null));
    }
    
    public function groupBy(callable $function)
    {
        return new GroupedTraversable(new Iterators\GroupedIterator($this->valuesIterator, $function));
    }
    
    public function join($values)
    {
        return new JoiningOnTraversable(
                $this->valuesIterator,
                Utilities::toIterator($values),
                false);
    }
    
    public function groupJoin($values)
    {
        return new JoiningOnTraversable(
                $this->valuesIterator,
                Utilities::toIterator($values),
                true);
    }
    
    public function unique()
    {
        return new self(new Iterators\UniqueIterator($this->valuesIterator));
    }
    
    public function select(callable $function)
    {
        return new self(new Iterators\ProjectionIterator(
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
        
        return new self(new Iterators\FlatteningIterator($projectionIterator));
    }
    
    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Set Operations">
    public function union($values)
    {
        return new self(new Iterators\UnionIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }
    
    public function intersect($values)
    {
        return new self(new Iterators\IntersectionIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }
    
    public function difference($values)
    {
        return new self(new Iterators\DifferenceIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }
    
    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Operations">
    public function append($values)
    {
        return new self(new Iterators\FlatteningIterator(new \ArrayIterator([$this->valuesIterator, Utilities::toIterator($values)])));
    }
    
    public function whereIn($values)
    {
        return new self(new Iterators\WhereInIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }
    
    public function except($values)
    {
        return new self(new Iterators\ExceptIterator(
                $this->valuesIterator,
                Utilities::toIterator($values)));
    }
    
    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Array Access">
    public function offsetExists($index)
    {
        return $this->valuesIterator instanceof \ArrayAccess ? $this->valuesIterator->offsetExists($index) : isset($this->asArray()[$index]);
    }
    
    public function offsetGet($index)
    {
        return $this->valuesIterator instanceof \ArrayAccess ? $this->valuesIterator->offsetGet($index) : $this->asArray()[$index];
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
        return $this->valuesIterator instanceof \Countable ? $this->valuesIterator->count() : count($this->asArray());
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
        }
        else {
            return array_map($function, $this->asArray());
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
}