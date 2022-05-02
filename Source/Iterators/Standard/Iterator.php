<?php

namespace Pinq\Iterators\Standard;

/**
 * Base class for iterators implementing the extended iterator interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class Iterator implements IIterator
{
    /**
     * @var mixed
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var boolean
     */
    private $valid = false;

    /**
     * @var boolean
     */
    private $requiresFirstFetch = false;

    public function __construct()
    {

    }

    final public function valid(): bool
    {
        if ($this->requiresFirstFetch) {
            $this->fetch();
        }

        return $this->valid;
    }

    #[\ReturnTypeWillChange]
    final public function key()
    {
        if ($this->requiresFirstFetch) {
            $this->fetch();
        }

        return $this->key;
    }

    #[\ReturnTypeWillChange]
    final public function &current()
    {
        if ($this->requiresFirstFetch) {
            $this->fetch();
        }

        return $this->value;
    }

    final public function rewind(): void
    {
        $this->valid = false;
        $this->doRewind();
        $this->requiresFirstFetch = true;
    }

    protected function doRewind()
    {

    }

    final public function next(): void
    {
        if ($this->requiresFirstFetch) {
            $this->fetch();
        }

        $this->fetch();
    }

    final public function fetch()
    {
        $this->requiresFirstFetch = false;
        if ($this->valid = (null !== ($element = $this->doFetch()))) {
            $this->key   = $element[0];
            $this->value =& $element[1];

            return $element;
        }
    }

    /**
     * @return array|null
     */
    abstract protected function doFetch();
}
