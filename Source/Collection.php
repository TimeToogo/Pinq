<?php

namespace Pinq;

use Pinq\Iterators\IIteratorScheme;

/**
 * The standard collection class, fully implements the collection API
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Collection extends Traversable implements ICollection, Interfaces\IOrderedCollection
{
    /**
     * The source collection.
     *
     * @var Collection|null
     */
    protected $source;

    public function __construct($values = [], IIteratorScheme $scheme = null, Collection $source = null)
    {
        parent::__construct($values, $scheme, $source);

        if ($source === null) {
            $this->toOrderedMap();
        }
    }

    public function asTraversable()
    {
        return new Traversable($this->elements, $this->scheme);
    }

    /**
     * {@inheritDoc}
     * @param Collection|null $source
     */
    public static function from($elements, Iterators\IIteratorScheme $scheme = null, Traversable $source = null)
    {
        if ($source !== null && !($source instanceof Collection)) {
            throw new PinqException(
                    'Cannot construct %s: expecting source to be type %s or null, %s given',
                    __CLASS__,
                    __CLASS__,
                    Utilities::getTypeOrClass($source));
        }

        return new static($elements, $scheme, $source);
    }

    protected function updateElements(\Traversable $elements)
    {
        $collectionElements = $this->toOrderedMap();

        $loadedElements = $this->scheme->createOrderedMap($elements);
        $collectionElements->clear();
        $collectionElements->setAll($loadedElements);
    }

    /**
     * @return Iterators\IOrderedMap
     */
    protected function toOrderedMap()
    {
        $this->elements = $this->asOrderedMap();

        return $this->elements;
    }

    public function clear()
    {
        if ($this->source !== null) {
            $this->source->removeRange($this->elements);
        } else {
            $this->updateElements($this->scheme->emptyIterator());
        }
    }

    public function join($values)
    {
        return new Connectors\JoiningCollection(
                $this,
                $this->scheme->joinIterator(
                        $this->elements,
                        $this->scheme->toIterator($values)
                ),
                $this->scopedSelfFactory());
    }

    public function groupJoin($values)
    {
        return new Connectors\JoiningCollection(
                $this,
                $this->scheme->groupJoinIterator(
                        $this->elements,
                        $this->scheme->toIterator($values),
                        $this->scopedSelfFactory()
                ),
                $this->scopedSelfFactory());
    }

    public function apply(callable $function)
    {
        if ($this->source !== null) {
            $this->scheme->walk($this->elements, $function);
        } else {
            $this->toOrderedMap()->walk($function);
        }
    }

    public function addRange($values)
    {
        if ($this->source !== null) {
            $this->source->addRange($values);
        } else {
            $this->updateElements(
                    $this->scheme->appendIterator(
                            $this->elements,
                            $this->scheme->toIterator($values)
                    )
            );
        }
    }

    public function remove($value)
    {
        $this->removeRange([$value]);
    }

    public function removeRange($values)
    {
        if ($this->source !== null) {
            $this->source->removeRange(
                    $this->scheme->intersectionIterator(
                            $this->elements,
                            $this->scheme->toIterator($values)
                    )
            );
        } else {
            $this->updateElements(
                    $this->scheme->exceptIterator(
                            $this->elements,
                            $this->scheme->toIterator($values)
                    )
            );
        }
    }

    public function removeWhere(callable $predicate)
    {
        $elementsToRemove = $this->scheme->createOrderedMap(
                $this->scheme->filterIterator(
                        $this->elements,
                        $predicate
                )
        );

        $this->removeRange($elementsToRemove);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->asOrderedMap()->offsetGet($key);
    }

    public function offsetSet($index, $value): void
    {
        if ($this->source !== null) {
            $this->source->offsetSet($index, $value);
        } else {
            $this->toOrderedMap()->offsetSet($index, $value);
        }
    }

    public function offsetUnset($index): void
    {
        if ($this->source !== null) {
            $this->source->offsetUnset($index);
        } else {
            $this->toOrderedMap()->offsetUnset($index);
        }
    }
}
