<?php

namespace Pinq\Iterators;

/**
 * Returns the values / keys projected by the supplied function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProjectionIterator extends IteratorIterator
{
    /**
     * @var callable|null
     */
    private $keyProjectionFunction;

    /**
     * @var callable|null
     */
    private $valueProjectionFunction;

    public function __construct(\Traversable $iterator, callable $keyProjectionFunction = null, callable $valueProjectionFunction = null)
    {
        parent::__construct($iterator);
        
        $this->keyProjectionFunction = $keyProjectionFunction === null ? 
                null : Utilities\Functions::allowExcessiveArguments($keyProjectionFunction);
        $this->valueProjectionFunction = $valueProjectionFunction === null ?
                null : Utilities\Functions::allowExcessiveArguments($valueProjectionFunction);
    }
    
    protected function doFetch(&$key, &$value)
    {
        if($this->iterator->fetch($key, $value)) {
            $keyFunction = $this->keyProjectionFunction;
            $valueFunction = $this->valueProjectionFunction;
            
            $keyCopy = $key;
            $valueCopy = $value;
            
            if($keyFunction !== null) {
                $keyCopyForKey = $keyCopy;
                $valueCopyForKey = $valueCopy;

                $key = $keyFunction($valueCopyForKey, $keyCopyForKey);
            }
            
            if($valueFunction !== null) {
                $keyCopyForValue = $keyCopy;
                $valueCopyForValue = $valueCopy;

                $value = $valueFunction($valueCopyForValue, $keyCopyForValue);
            }
            
            return true;
        }
        
        return false;
    }
}
