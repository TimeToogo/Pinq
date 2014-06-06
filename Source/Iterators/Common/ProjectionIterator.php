<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the projection iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait ProjectionIterator
{
    /**
     * @var callable|null
     */
    private $keyProjectionFunction;

    /**
     * @var callable|null
     */
    private $valueProjectionFunction;

    protected function __constructIterator(callable $keyProjectionFunction = null, callable $valueProjectionFunction = null)
    {
        $this->keyProjectionFunction = $keyProjectionFunction === null ? 
                null : Functions::allowExcessiveArguments($keyProjectionFunction);
        $this->valueProjectionFunction = $valueProjectionFunction === null ?
                null : Functions::allowExcessiveArguments($valueProjectionFunction);
    }
    
    final protected function projectKeyAndValue(&$key, &$value)
    {
        $keyProjectionFunction = $this->keyProjectionFunction;
        $valueProjectionFunction = $this->valueProjectionFunction;
        
        $keyCopy = $key;
        $valueCopy = $value;

        if($keyProjectionFunction !== null) {
            $keyCopyForKey = $keyCopy;
            $valueCopyForKey = $valueCopy;

            $key = $keyProjectionFunction($valueCopyForKey, $keyCopyForKey);
        }

        if($valueProjectionFunction !== null) {
            $keyCopyForValue = $keyCopy;
            $valueCopyForValue = $valueCopy;

            $value = $valueProjectionFunction($valueCopyForValue, $keyCopyForValue);
        }
    }
}
