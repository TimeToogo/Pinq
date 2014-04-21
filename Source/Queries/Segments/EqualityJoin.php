<?php

namespace Pinq\Queries\Segments; 

use \Pinq\FunctionExpressionTree;

/**
 * Query segment for an equality join base on a key select for the outer values
 * an another for the inner values 
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EqualityJoin extends JoinBase
{
    /**
     * The outer key selector function
     * 
     * @var FunctionExpressionTree
     */
    private $OuterKeyFunction;
    
    /**
     * The inner key selector function
     * 
     * @var FunctionExpressionTree
     */
    private $InnerKeyFunction;

    public function __construct(
            $Values, 
            $IsGroupJoin,
            FunctionExpressionTree $OuterKeyFunction,
            FunctionExpressionTree $InnerKeyFunction,
            FunctionExpressionTree $JoiningFunction)
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
     * @return FunctionExpressionTree
     */
    public function GetOuterKeyFunctionExpressionTree()
    {
        return $this->OuterKeyFunction;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function GetInnerKeyFunctionExpressionTree()
    {
        return $this->InnerKeyFunction;
    }
    
    public function Update(
            $Values, 
            $IsGroupJoin, 
            FunctionExpressionTree $OuterKeyFunction, 
            FunctionExpressionTree $InnerKeyFunction, 
            FunctionExpressionTree $JoiningFunction)
    {
        if($this->Values === $Values
                && $this->IsGroupJoin === $IsGroupJoin
                && $this->OuterKeyFunction === $OuterKeyFunction
                && $this->InnerKeyFunction === $InnerKeyFunction
                && $this->JoiningFunction === $JoiningFunction) {
            return $this;
        }
        
        return new self($Values, $IsGroupJoin, $OuterKeyFunction, $InnerKeyFunction, $JoiningFunction);
    }
}
