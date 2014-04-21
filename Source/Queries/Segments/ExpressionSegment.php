<?php

namespace Pinq\Queries\Segments; 

use \Pinq\FunctionExpressionTree;

/**
 * Base class for a query segment with an function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ExpressionSegment extends Segment
{
    /**
     * @var FunctionExpressionTree
     */
    private $FunctionExpressionTree;

    final public function __construct(FunctionExpressionTree $FunctionExpressionTree)
    {
        $this->FunctionExpressionTree = $FunctionExpressionTree;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function GetFunctionExpressionTree()
    {
        return $this->FunctionExpressionTree;
    }
    
    public function Update(\Pinq\FunctionExpressionTree $FunctionExpressionTree) {
        if($this->FunctionExpressionTree === $FunctionExpressionTree) {
            return $this;
        }
        
        return new static($FunctionExpressionTree);
    }
}
