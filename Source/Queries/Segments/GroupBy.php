<?php

namespace Pinq\Queries\Segments; 

use \Pinq\FunctionExpressionTree;

/**
 * Query segment for grouping the values base on the supplied 
 * grouping functions.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class GroupBy extends Segment
{
    /**
     * @var FunctionExpressionTree[]
     */
    private $FunctionExpressionTrees;

    public function __construct(array $FunctionExpressionTrees)
    {
        $this->FunctionExpressionTrees = $FunctionExpressionTrees;
    }

    public function GetType()
    {
        return self::GroupBy;
    }
    
    public function GetFunctionExpressionTrees()
    {
        return $this->FunctionExpressionTrees;
    }
    
    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkGroupBy($this);
    }
    
    public function AndBy(FunctionExpressionTree $FunctionExpressionTree) 
    {
        return new self(array_merge($this->FunctionExpressionTrees, [$FunctionExpressionTree]));
    }
    
    public function Update(array $FunctionExpressionTrees)
    {
        if($this->FunctionExpressionTrees === $FunctionExpressionTrees) {
            return $this;
        }
        
        return new self($FunctionExpressionTrees);
    }
}
