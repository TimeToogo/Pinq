<?php

namespace Pinq\Iterators;

/**
 * Iterates the inner iterator and for every value, it is then iterated as
 * the resulting values (pretty much a nested foreach loop)
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
     * @var Pinq\IIterator
     */
    protected $outerIterator;

    /**
     * @var Pinq\IIterator
     */
    protected $innerIterator;

    public function __construct(\Traversable $iterator)
    {
        $this->outerIterator = \Pinq\Utilities::toIterator($iterator);
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

            $this->innerIterator = \Pinq\Utilities::toIterator($outerValue);
            $this->innerIterator->rewind();
        }
        
        $key = $this->count++;

        return true;
    }
}
