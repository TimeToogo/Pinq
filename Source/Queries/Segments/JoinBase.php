<?php

namespace Pinq\Queries\Segments; 

abstract class JoinBase extends Segment
{
    protected $Values;
    protected $IsGroupJoin;
    protected $JoiningFunction;

    public function __construct($Values, $IsGroupJoin, callable $JoiningFunction)
    {
        $this->Values = $Values;
        $this->IsGroupJoin = $IsGroupJoin;
        $this->JoiningFunction = $JoiningFunction;
    }
    
    /**
     * @return array|\Traversable
     */
    final public function GetValues()
    {
        return $this->Values;
    }
    
    /**
     * @return boolean
     */
    final public function IsGroupJoin()
    {
        return $this->IsGroupJoin;
    }
    
    /**
     * @return callable
     */
    final public function GetJoiningFunction()
    {
        return $this->JoiningFunction;
    }
}
