<?php

namespace Pinq\Iterators;

/**
 * Iterates the keys and values such that they are array compatible
 * Numerically reindexes non scalar keys.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ArrayCompatibleIterator extends IteratorIterator
{
    /**
     * @var int
     */
    private $maxKey = 0;
    
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
    }
    
    protected function fetchInner(\Iterator $iterator, &$key, &$value) {
        if(parent::fetchInner($iterator, $key, $value)) {
            if($key === null || is_scalar($key)) {
                $intKey = (int)$key;
                if((string)$intKey === (string)$key && $intKey >= $this->maxKey) {
                    $this->maxKey = $intKey + 1;
                }
            } else {
                $key = $this->maxKey++;
            }
            
            return true;
        }
        
        return false;
    }
    
    public function onRewind()
    {
        $this->maxKey = 0;
        parent::onRewind();
    }
}
