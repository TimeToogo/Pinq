<?php

namespace Pinq\Iterators\Common;

/**
 * Contains the common functionality for a ISet implementation
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait Set
{
    /**
     * The array containing the values keyed by their identity hash.
     *
     * @var array
     */
    protected $values = [];

    /**
     * The amount of values in the set.
     *
     * @var int
     */
    protected $length = 0;

    public function count(): int
    {
        return $this->length;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->values = [];
        $this->length = 0;
    }

    /**
     * {@inheritDoc}
     */
    public function contains($value)
    {
        return $value === null ? array_key_exists(Identity::hash(null), $this->values) : isset($this->values[Identity::hash($value)]);
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        return $this->addRef($value);
    }

    /**
     * {@inheritDoc}
     */
    public function addRef(&$value)
    {
        $identityHash = Identity::hash($value);

        if (isset($this->values[$identityHash]) || array_key_exists($identityHash, $this->values)) {
            return false;
        }

        $this->values[$identityHash] =& $value;
        $this->length++;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($value)
    {
        $identityHash = Identity::hash($value);

        if (!isset($this->values[$identityHash]) && !array_key_exists($identityHash, $this->values)) {
            return false;
        }

        unset($this->values[$identityHash]);
        $this->length--;

        return true;
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return true;
    }
}
