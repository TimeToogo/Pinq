<?php

namespace Pinq\Queries\Segments; 

class Join extends JoinBase
{
    private $OnFunction;

    public function __construct($Values, $IsGroupJoin, callable $OnFunction, callable $JoiningFunction)
    {
        parent::__construct($Values, $IsGroupJoin, $JoiningFunction);
        $this->OnFunction = $OnFunction;
    }

    public function GetType()
    {
        return self::Join;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkJoin($this);
    }

    /**
     * @return callable
     */
    public function GetOnFunction()
    {
        return $this->OnFunction;
    }
    
    public function Update($Values, $IsGroupJoin, callable $OnFunction, callable $JoiningFunction)
    {
        if($this->Values === $Values
                && $this->IsGroupJoin === $IsGroupJoin
                && $this->OnFunction === $OnFunction
                && $this->JoiningFunction = $JoiningFunction) {
            return $this;
        }
        
        return new self($Values, $IsGroupJoin, $OnFunction, $JoiningFunction);
    }
}
