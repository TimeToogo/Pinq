<?php

namespace Pinq;

use Pinq\Iterators\IIteratorScheme;

/**
 * The standard traversable class, fully implements the traversable API
 * using iterators to achieve lazy evaluation
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Traversable implements ITraversable, Interfaces\IOrderedTraversable
{
    /**
     * The iterator scheme used in the traversable instance.
     *
     * @var IIteratorScheme
     */
    protected $scheme;

    /**
     * The source traversable.
     *
     * @var Traversable|null
     */
    protected $source;

    /**
     * The element iterator for the traversable.
     *
     * @var \Traversable
     */
    protected $elements;

    public function __construct($elements = [], IIteratorScheme $scheme = null, Traversable $source = null)
    {
        $this->scheme   = $scheme ?: Iterators\SchemeProvider::getDefault();
        $this->source   = $source;
        $this->elements = $this->scheme->toIterator($elements);
    }

    /**
     * Constructs a new traversable object from the supplied elements.
     *
     * @param array|\Traversable   $elements
     * @param IIteratorScheme|null $scheme
     * @param Traversable|null     $source
     *
     * @return ITraversable
     */
    public static function from($elements, IIteratorScheme $scheme = null, Traversable $source = null)
    {
        return new static($elements, $scheme, $source);
    }

    /**
     * Returns a callable for the traversable constructor.
     *
     * @param IIteratorScheme|null $scheme
     * @param Traversable|null     $source
     *
     * @return callable
     */
    public static function factory(IIteratorScheme $scheme = null, Traversable $source = null)
    {
        //static:: doesn't work in closures?
        $static = get_called_class();

        return function ($elements) use ($static, $scheme, $source) {
            return $static::from($elements, $scheme, $source);
        };
    }

    /**
     * Returns a callable factory to construct an equivalent
     * instance with the supplied elements.
     *
     * @return callable
     */
    final protected function scopedSelfFactory()
    {
        return function ($elements) {
            return $this->constructScopedSelf($elements);
        };
    }

    /**
     * Returns a new instance of the traversable with the scoped elements
     * and same scheme and source.
     *
     * @param array|\Traversable $elements
     *
     * @return static
     */
    protected function constructScopedSelf($elements)
    {
        return static::from($elements, $this->scheme, $this->source ?: $this);
    }

    public function isSource()
    {
        return $this->source === null;
    }

    public function getSource()
    {
        return $this->source ?: $this;
    }

    public function asArray()
    {
        return $this->scheme->toArray($this->elements);
    }

    public function getIterator(): \Traversable
    {
        return $this->scheme->arrayCompatibleIterator($this->getTrueIterator());
    }

    public function getTrueIterator()
    {
        return $this->elements;
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
        return new Collection($this->elements, $this->scheme);
    }

    /**
     * @return Iterators\IOrderedMap
     */
    protected function asOrderedMap()
    {
        return $this->elements instanceof Iterators\IOrderedMap ?
                $this->elements : $this->scheme->createOrderedMap($this->elements);
    }

    public function iterate(callable $function)
    {
        $this->scheme->walk($this->elements, $function);
    }

    // <editor-fold defaultstate="collapsed" desc="Querying">

    public function first()
    {
        foreach ($this->elements as $value) {
            return $value;
        }

        return null;
    }

    public function last()
    {
        $value = null;

        foreach ($this->elements as $value) {

        }

        return $value;
    }

    public function where(callable $predicate)
    {
        return $this->constructScopedSelf(
                $this->scheme->filterIterator(
                        $this->elements,
                        $predicate
                )
        );
    }

    public function orderByAscending(callable $function)
    {
        return $this->constructScopedSelf(
                $this->scheme->orderedIterator(
                        $this->elements,
                        $function,
                        true
                )
        );
    }

    public function orderByDescending(callable $function)
    {
        return $this->constructScopedSelf(
                $this->scheme->orderedIterator(
                        $this->elements,
                        $function,
                        false
                )
        );
    }

    public function orderBy(callable $function, $direction)
    {
        return $direction === Direction::DESCENDING ?
                $this->orderByDescending($function) : $this->orderByAscending($function);
    }

    /**
     * Verifies that the traversable is ordered.
     *
     * @param string $method The called method name
     *
     * @return Iterators\IOrderedIterator
     * @throws PinqException
     */
    private function validateIsOrdered($method)
    {
        $innerIterator = $this->elements;
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
        return $this->constructScopedSelf(
                $this->validateIsOrdered(__METHOD__)->thenOrderBy(
                        $function,
                        $direction !== Direction::DESCENDING
                )
        );
    }

    public function thenByAscending(callable $function)
    {
        return $this->constructScopedSelf(
                $this->validateIsOrdered(__METHOD__)
                        ->thenOrderBy($function, true)
        );
    }

    public function thenByDescending(callable $function)
    {
        return $this->constructScopedSelf(
                $this->validateIsOrdered(__METHOD__)
                        ->thenOrderBy($function, false)
        );
    }

    public function skip($amount)
    {
        return $this->constructScopedSelf(
                $this->scheme->rangeIterator(
                        $this->elements,
                        $amount,
                        null
                )
        );
    }

    public function take($amount)
    {
        return $this->constructScopedSelf(
                $this->scheme->rangeIterator(
                        $this->elements,
                        0,
                        $amount
                )
        );
    }

    public function slice($start, $amount)
    {
        return $this->constructScopedSelf(
                $this->scheme->rangeIterator(
                        $this->elements,
                        $start,
                        $amount
                )
        );
    }

    public function indexBy(callable $function)
    {
        return $this->constructScopedSelf(
                $this->scheme->uniqueKeyIterator(
                        $this->scheme->projectionIterator(
                                $this->elements,
                                $function,
                                null
                        )
                )
        );
    }

    public function keys()
    {
        return $this->constructScopedSelf(
                $this->scheme->reindexerIterator(
                        $this->scheme->projectionIterator(
                                $this->elements,
                                null,
                                function ($value, $key) {
                                    return $key;
                                }
                        )
                )
        );
    }

    public function reindex()
    {
        return $this->constructScopedSelf(
                $this->scheme->reindexerIterator($this->elements)
        );
    }

    public function groupBy(callable $function)
    {
        return $this->constructScopedSelf(
                $this->scheme->groupedIterator(
                        $this->elements,
                        $function,
                        $this->scopedSelfFactory()
                )
        );
    }

    public function join($values)
    {
        return new Connectors\JoiningTraversable(
                $this->scheme,
                $this->scheme->joinIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                ),
                $this->scopedSelfFactory());
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningTraversable(
                $this->scheme,
                $this->scheme->groupJoinIterator(
                        $this->elements,
                        $this->scheme->toIterator($values),
                        $this->scopedSelfFactory()
                ),
                $this->scopedSelfFactory());
    }

    public function unique()
    {
        return $this->constructScopedSelf($this->scheme->uniqueIterator($this->elements));
    }

    public function select(callable $function)
    {
        return $this->constructScopedSelf(
                $this->scheme->projectionIterator(
                        $this->elements,
                        null,
                        $function
                )
        );
    }

    public function selectMany(callable $function)
    {
        $projectionIterator =
                $this->scheme->projectionIterator(
                        $this->elements,
                        null,
                        $function
                );

        return $this->constructScopedSelf($this->scheme->flattenedIterator($projectionIterator));
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Set Operations">

    public function union($values)
    {
        return $this->constructScopedSelf(
                $this->scheme->unionIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                )
        );
    }

    public function intersect($values)
    {
        return $this->constructScopedSelf(
                $this->scheme->intersectionIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                )
        );
    }

    public function difference($values)
    {
        return $this->constructScopedSelf(
                $this->scheme->differenceIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                )
        );
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Multiset Operations">

    public function append($values)
    {
        return $this->constructScopedSelf(
                $this->scheme->appendIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                )
        );
    }

    public function whereIn($values)
    {
        return $this->constructScopedSelf(
                $this->scheme->whereInIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                )
        );
    }

    public function except($values)
    {
        return $this->constructScopedSelf(
                $this->scheme->exceptIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                )
        );
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Array Access">

    public function offsetExists($index): bool
    {
        foreach ($this->keys() as $key) {
            if ($key === $index) {
                return true;
            }
        }

        return false;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($index)
    {
        foreach ($this->select(function ($value, $key) { return [$key, $value]; }) as $element) {
            if ($element[0] === $index) {
                return $element[1];
            }
        }

        return false;
    }

    public function offsetSet($index, $value): void
    {
        throw PinqException::notSupported(__METHOD__);
    }

    public function offsetUnset($index): void
    {
        throw PinqException::notSupported(__METHOD__);
    }

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Aggregates">

    public function count(): int
    {
        if($this->elements instanceof \Countable) {
            return $this->elements->count();
        }

        $count = 0;

        foreach($this->elements as $value) {
            $count++;
        }

        return $count;
    }

    public function isEmpty()
    {
        foreach ($this->elements as $value) {
            return false;
        }

        return true;
    }

    public function contains($value)
    {
        foreach ($this->elements as $containedValue) {
            if ($containedValue === $value) {
                return true;
            }
        }

        return false;
    }

    public function aggregate(callable $function)
    {
        $hasValue       = false;
        $aggregateValue = null;

        foreach ($this->elements as $value) {
            if (!$hasValue) {
                $aggregateValue = $value;
                $hasValue       = true;
                continue;
            }

            $aggregateValue = $function($aggregateValue, $value);
        }

        return $aggregateValue;
    }

    private function mapIterator(callable $function = null)
    {
        if ($function === null) {
            return $this->elements;
        } else {
            return $this->scheme->projectionIterator(
                    $this->elements,
                    null,
                    $function
            );
        }
    }

    public function maximum(callable $function = null)
    {
        $max = null;

        foreach ($this->mapIterator($function) as $value) {
            if ($value > $max) {
                $max = $value;
            }
        }

        return $max;
    }

    public function minimum(callable $function = null)
    {
        $min = null;
        $first = true;

        foreach ($this->mapIterator($function) as $value) {
            if ($value < $min || $first) {
                $min   = $value;
                $first = false;
            }
        }

        return $min;
    }

    public function sum(callable $function = null)
    {
        $sum = null;

        foreach ($this->mapIterator($function) as $value) {
            $sum += $value;
        }

        return $sum;
    }

    public function average(callable $function = null)
    {
        $sum = null;
        $count = 0;

        foreach ($this->mapIterator($function) as $value) {
            $sum += $value;
            $count++;
        }

        return $count === 0 ? null : $sum / $count;
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
        $string = '';

        foreach ($this->mapIterator($function) as $value) {
            $string .= $delimiter . $value;
        }

        return $string === '' ? '' : substr($string, strlen($delimiter));
    }

    // </editor-fold>
}
