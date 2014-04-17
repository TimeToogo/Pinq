<?php

namespace Pinq\Iterators;

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
}
