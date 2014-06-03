<?php

namespace Pinq\Iterators;

/**
 * Base class for a join iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class JoinIteratorBase extends Iterator implements \Iterator
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
     * @var mixed
     */
    private $currentOuterKey = null;

    /**
     * @var \Iterator
     */
    private $currentInnerGroupIterator;

    /**
     * @var callable
     */
    protected $joiningFunction;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator, callable $joiningFunction)
    {
        $this->outerIterator = \Pinq\Utilities::toIterator($outerIterator);
        $this->innerIterator = \Pinq\Utilities::toIterator($innerIterator);
        $this->joiningFunction = Utilities\Functions::allowExcessiveArguments($joiningFunction);
    }

    public function onRewind()
    {
        if (!$this->isInitialized) {
            $this->initialize();
            $this->isInitialized = true;
        }
        
        $this->currentOuterValue = null;
        $this->outerIterator->rewind();
        $this->currentInnerGroupIterator = new \EmptyIterator();
        $this->count = 0;
    }

    abstract protected function initialize();

    final public function onNext()
    {
        $this->currentInnerGroupIterator->next();
        $this->count++;
    }
    
    protected function fetch(&$key, &$value)
    {
        while (!$this->currentInnerGroupIterator->valid()) {
            if (!$this->outerIterator->valid()) {
                return false;
            }

            $this->currentOuterKey = $this->outerIterator->key();
            $this->currentOuterValue = $this->outerIterator->current();
            $this->currentInnerGroupIterator = $this->getInnerGroupIterator($this->currentOuterValue, $this->currentOuterKey);
            $this->currentInnerGroupIterator->rewind();
            $this->outerIterator->next();
        }
        
        $joiningFunction = $this->joiningFunction;
        
        $key = $this->count;
        $value = $joiningFunction(
                $this->currentOuterValue, 
                $this->currentInnerGroupIterator->current(),
                $this->currentOuterKey,
                $this->currentInnerGroupIterator->key());
        
        return true;
    }

    /**
     * @return \Iterator
     */
    abstract protected function getInnerGroupIterator($outerValue, $outerKey);
}
