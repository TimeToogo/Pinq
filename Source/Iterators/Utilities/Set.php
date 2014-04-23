<?php

namespace Pinq\Iterators\Utilities;

/**
 * Represents set of unique values.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Set implements \IteratorAggregate
{
    /**
     * The dictionary containing the unique values as keys
     *
     * @var Dictionary<mixed, true>
     */
    private $dictionary;

    public function __construct($values = null)
    {
        $this->dictionary = new Dictionary();

        if ($values !== null) {
            $this->addRange($values);
        }
    }

    /**
     * Returns whether the values in contained in the set
     *
     * @param mixed $value
     * @return boolean
     */
    public function contains($value)
    {
        return $this->dictionary->contains($value);
    }

    /**
     * Attempts to add the value to the set, will fail if the value
     * is already contained in the set
     *
     * @param mixed $value
     * @return boolean Whether the value was successfully added
     */
    public function add($value)
    {
        if ($this->dictionary->contains($value)) {
            return false;
        }

        $this->dictionary->set($value, true);

        return true;
    }

    /**
     * Attempts to add a range of the value to the set
     *
     * @param array|\Traversable $values
     * @return void
     */
    public function addRange($values)
    {
        foreach ($values as $value) {
            $this->dictionary->set($value, true);
        }
    }

    /**
     * Attempts to remove the value from the set, will fail if the value
     * is not contained in the set
     *
     * @param mixed $value
     * @return boolean Whether the value was successfully removed
     */
    public function remove($value)
    {
        if (!$this->dictionary->contains($value)) {
            return false;
        }

        $this->dictionary->remove($value);

        return true;
    }

    /**
     * Attempts to remove a range of the value to the set
     *
     * @param array|\Traversable $values
     * @return void
     */
    public function removeRange($values)
    {
        foreach ($values as $value) {
            $this->dictionary->remove($value);
        }
    }

    public function getIterator()
    {
        return $this->dictionary->getIterator();
    }
}
