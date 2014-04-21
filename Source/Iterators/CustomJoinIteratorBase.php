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
    protected $JoinOnFunction;
    
    /**
     * @var array
     */
    protected $InnerValues;
    
    public function __construct(
            \Traversable $OuterIterator,
            \Traversable $InnerIterator,
            callable $JoinOnFunction,
            callable $JoiningFunction)
    {
        parent::__construct($OuterIterator, $InnerIterator, $JoiningFunction);
        $this->JoinOnFunction = $JoinOnFunction;
    }
    
    final protected function Initialize()
    {
        $this->InnerValues = \Pinq\Utilities::ToArray($this->InnerIterator);
    }
    
    final protected function GetInnerGroupIterator($OuterValue)
    {
        $JoinOnFunction = $this->JoinOnFunction;
        
        $InnerValueFilterFunction = 
                function ($InnerValue) use ($OuterValue, $JoinOnFunction) {
                    return $JoinOnFunction($OuterValue, $InnerValue);
                };
                
        return $this->GetInnerGroupValuesIterator($InnerValueFilterFunction);
    }
    
    protected abstract function GetInnerGroupValuesIterator(callable $InnerValueFilterFunction);
}
