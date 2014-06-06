<?php

namespace Pinq\Iterators\Standard;

use Pinq\Iterators\Common;

/**
 * Implementation of the flattening iterator using the fetch method.
 *
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class FlatteningIterator extends Iterator
{
    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var IIterator
     */
    protected $outerIterator;

    /**
     * @var IIterator
     */
    protected $innerIterator;

    public function __construct(IIterator $iterator)
    {
        parent::__construct();
        $this->outerIterator = $iterator;
        $this->innerIterator = new EmptyIterator();
    }

    public function doRewind()
    {
        $this->count = 0;
        $this->outerIterator->rewind();
    }
    
    protected function doFetch(&$key, &$value)
    {
        while (!$this->innerIterator->fetch($innerKey, $value)) {
            if (!$this->outerIterator->fetch($outerKey, $outerValue)) {
                return false;
            }
            
            if(!($outerValue instanceof IIterator)) {
                throw new \Pinq\PinqException(
                        '%s expects all returned value to be of type %s: %s given',
                        __CLASS__,
                        IIterator::IITERATOR_TYPE,
                        \Pinq\Utilities::getTypeOrClass($outerValue));
            }
            
            $this->innerIterator = $outerValue;
            $this->innerIterator->rewind();
        }
        
        $key = $this->count++;

        return true;
    }
}
