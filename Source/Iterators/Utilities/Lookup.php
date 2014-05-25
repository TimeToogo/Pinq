<?php

namespace Pinq\Iterators\Utilities;

/**
 * Represents a  range grouped values determined from a supplied grouping function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Lookup implements \IteratorAggregate
{
    /**
     * The dictionary containing the keyed groups
     *
     * @var Dictionary<mixed, \ArrayObject>
     */
    private $dictionary;

    private function __construct()
    {
        $this->dictionary = new Dictionary();
    }

    public static function fromGroupingFunction(callable $groupingFunction, \Traversable $values, array &$groupKeys = null)
    {
        $groupKeys = [];
        $lookup = new self();
        $values = \Pinq\Utilities::toArray($values);
        $groupByValues = [];
        foreach ($values as $key => $value) {
            $groupByValues[$key] = $groupingFunction($value, $key);
        }

        foreach ($groupByValues as $valueKey => $groupKey) {
            if ($lookup->dictionary->contains($groupKey)) {
                $group = $lookup->dictionary->get($groupKey);
                $group[$valueKey] = $values[$valueKey];
            } else {
                $groupKeys[] = $groupKey;
                $lookup->dictionary->set(
                        $groupKey,
                        new \ArrayObject([$valueKey => $values[$valueKey]]));
            }
        }

        return $lookup;
    }

    /**
     * Returns the group of values from the specified key
     *
     * @param mixed $key
     * @return array
     */
    public function get($key)
    {
        return $this->dictionary->get($key)->getArrayCopy();
    }

    /**
     * Returns all the groups as an array
     *
     * @return \ArrayObject[]
     */
    public function asArray()
    {
        return $this->dictionary->values();
    }

    /**
     * Returns whether there is a specified group for the given key
     *
     * @param mixed $key
     * @return boolean
     */
    public function contains($key)
    {
        return $this->dictionary->contains($key);
    }

    public function getIterator()
    {
        $dictionary = $this->dictionary;
        
        return new \Pinq\Iterators\ProjectionIterator(
                $dictionary->getIterator(), 
                function ($key) { return $key; }, 
                function ($key) use($dictionary) { return $dictionary->get($key); });
    }
}
