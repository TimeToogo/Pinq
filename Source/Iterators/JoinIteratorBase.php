<?php

namespace Pinq\Iterators;

/**
 * Base class for a join iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class JoinIteratorBase implements \Iterator
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var boolean
     */
    protected $isInitialized = false;

    /**
     * @var \Iterator
     */
    protected $outerIterator;

    /**
     * @var \Iterator
     */
    protected $innerIterator;

    /**
     * @var mixed
     */
    private $currentOuterValue = null;

    /**
     * @var \Iterator
     */
    private $currentInnerGroupIterator;

    /**
     * @var callable
     */
    protected $joiningFunction;

    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator, callable $joiningFunction)
    {
        $this->outerIterator = \Pinq\Utilities::toIterator($outerIterator);
        $this->innerIterator = \Pinq\Utilities::toIterator($innerIterator);
        $this->joiningFunction = $joiningFunction;
    }

    final public function key()
    {
        return $this->count;
    }

    final public function current()
    {
        $joiningFunction = $this->joiningFunction;

        return $joiningFunction($this->currentOuterValue, $this->currentInnerGroupIterator->current());
    }

    final public function next()
    {
        $this->currentInnerGroupIterator->next();
        $this->count++;
    }

    final public function valid()
    {
        while (!$this->currentInnerGroupIterator->valid()) {
            if (!$this->outerIterator->valid()) {
                return false;
            }

            $this->currentOuterValue = $this->outerIterator->current();
            $this->currentInnerGroupIterator = $this->getInnerGroupIterator($this->currentOuterValue);
            $this->outerIterator->next();
        }

        return true;
    }

    /**
     * @return \Iterator
     */
    abstract protected function getInnerGroupIterator($outerValue);

    public function rewind()
    {
        if (!$this->isInitialized) {
            $this->initialize();
            $this->isInitialized = true;
        }
        
        $this->currentOuterValue = null;
        $this->outerIterator->rewind();
        $this->currentInnerGroupIterator = new \ArrayIterator();
        $this->count = 0;
    }

    abstract protected function initialize();
}
