<?php 

namespace Pinq\Iterators;

/**
 * Base class for a join iterator with a custom join on function.
 * The equi-join lookup optimization cannot be made and the function must be run
 * for every outer/inner pair.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class CustomJoinIteratorBase extends JoinIteratorBase
{
    /**
     * @var callable
     */
    protected $joinOnFunction;
    
    /**
     * @var array
     */
    protected $innerValues;
    
    public function __construct(\Traversable $outerIterator, \Traversable $innerIterator, callable $joinOnFunction, callable $joiningFunction)
    {
        parent::__construct($outerIterator, $innerIterator, $joiningFunction);
        $this->joinOnFunction = $joinOnFunction;
    }
    
    protected final function initialize()
    {
        $this->innerValues = \Pinq\Utilities::toArray($this->innerIterator);
    }
    
    protected final function getInnerGroupIterator($outerValue)
    {
        $joinOnFunction = $this->joinOnFunction;
        $innerValueFilterFunction = function ($innerValue) use($outerValue, $joinOnFunction) {
            return $joinOnFunction($outerValue, $innerValue);
        };
        
        return $this->getInnerGroupValuesIterator($innerValueFilterFunction);
    }
    
    protected abstract function getInnerGroupValuesIterator(callable $innerValueFilterFunction);
}