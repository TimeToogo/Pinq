<?php

namespace Pinq\Queries\Segments; 

class EqualityJoin extends JoinBase
{
    private $OuterKeyFunction;
    private $InnerKeyFunction;

    public function __construct($Values, $IsGroupJoin, callable $OuterKeyFunction, callable $InnerKeyFunction, callable $JoiningFunction)
    {
        parent::__construct($Values, $IsGroupJoin, $JoiningFunction);
        $this->OuterKeyFunction = $OuterKeyFunction;
        $this->InnerKeyFunction = $InnerKeyFunction;
    }

    public function GetType()
    {
        return self::EqualityJoin;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkEqualityJoin($this);
    }

    /**
     * @return callable
     */
    public function GetOuterKeyFunction()
    {
        return $this->OuterKeyFunction;
    }

    /**
     * @return callable
     */
    public function GetInnerKeyFunction()
    {
        return $this->InnerKeyFunction;
    }
    
    public function Update($Values, $IsGroupJoin, callable $OuterKeyFunction, callable $InnerKeyFunction, callable $JoiningFunction)
    {
        if($this->Values === $Values
                && $this->IsGroupJoin === $IsGroupJoin
                && $this->OuterKeyFunction === $OuterKeyFunction
                && $this->OuterKeyFunction === $InnerKeyFunction
                && $this->JoiningFunction === $JoiningFunction) {
            return $this;
        }
        
        return new self($Values, $IsGroupJoin, $OuterKeyFunction, $InnerKeyFunction, $JoiningFunction);
    }
}
