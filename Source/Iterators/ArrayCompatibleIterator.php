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
    
    /**
     * @var Utilities\OrderedMap
     */
    private $nonScalarKeyMap;
    
    public function __construct(\Traversable $iterator)
    {
        parent::__construct($iterator);
    }
    
    public function doRewind()
    {
        $this->maxKey = 0;
        $this->nonScalarKeyMap = new Utilities\OrderedMap();
        parent::doRewind();
    }
    
    protected function doFetch(&$key, &$value) {
        
        if($this->iterator->fetch($key, $value)) {
            if($key === null || is_scalar($key)) {
                $intKey = (int)$key;
                if((string)$intKey === (string)$key && $intKey >= $this->maxKey) {
                    $this->maxKey = $intKey + 1;
                }
            } elseif($this->nonScalarKeyMap->contains($key)) {
                $key = $this->nonScalarKeyMap->get($key);
            } else{
                $originalKey = $key;
                $key = $this->maxKey++;
                $this->nonScalarKeyMap->set($originalKey, $key);
            }
            
            return true;
        }
        
        return false;
    }
}
