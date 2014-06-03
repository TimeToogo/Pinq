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
     * @var mixed
     */
    protected $currentOuterKey;

    /**
     * @var mixed
     */
    protected $currentOuterValue;

    /**
     * @var Pinq\IIterator
     */
    protected $outerIterator;

    /**
     * @var Pinq\IIterator
     */
    protected $innerIterator;

    /**
     * @var Pinq\IIterator
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

    public function doRewind()
    {
        if (!$this->isInitialized) {
            $this->initialize();
            $this->isInitialized = true;
        }
        
        $this->outerIterator->rewind();
        $this->currentInnerGroupIterator = new EmptyIterator();
        $this->count = 0;
    }

    abstract protected function initialize();
    
    protected function doFetch(&$key, &$value)
    {
        while (!$this->currentInnerGroupIterator->fetch($innerKey, $innerValue)) {
            if (!$this->outerIterator->fetch($this->currentOuterKey, $this->currentOuterValue)) {
                return false;
            }

            $this->currentInnerGroupIterator = $this->getInnerGroupIterator($this->currentOuterValue, $this->currentOuterKey);
            $this->currentInnerGroupIterator->rewind();
        }
        
        $joiningFunction = $this->joiningFunction;
        
        $key = $this->count++;
        $value = $joiningFunction($this->currentOuterValue, $innerValue, $this->currentOuterKey, $innerKey);
        
        return true;
    }

    /**
     * @return Pinq\IIterator
     */
    abstract protected function getInnerGroupIterator($outerValue, $outerKey);
}
