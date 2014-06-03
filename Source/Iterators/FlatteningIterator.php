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
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @var \Iterator
     */
    protected $currentIterator;

    public function __construct(\Traversable $iterator)
    {
        $this->iterator = \Pinq\Utilities::toIterator($iterator);
        $this->currentIterator = new \EmptyIterator();
    }
    
    public function isArrayCompatible()
    {
        return true;
    }
    
    public function requiresKeyMapping()
    {
        return false;
    }

    public function onRewind()
    {
        $this->count = 0;
        $this->iterator->rewind();
    }
    
    protected function onNext()
    {
        $this->count++;
        $this->currentIterator->next();
    }
    
    protected function fetch(&$key, &$value)
    {
        while (!$this->currentIterator->valid()) {
            if (!$this->iterator->valid()) {
                return false;
            }

            $this->currentIterator = \Pinq\Utilities::toIterator($this->iterator->current());
            $this->currentIterator->rewind();
            
            $this->iterator->next();
        }
        
        $key = $this->count;
        $value = $this->currentIterator->current();

        return true;
    }
}
