<?php

namespace Pinq\Queries\Operations; 

use \Pinq\FunctionExpressionTree;

/**
 * Base class for an operation query containing a function expression tree
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ExpressionOperation extends Operation
{
    /**
     * @var FunctionExpressionTree
     */
    private $FunctionExpressionTree;

    public function __construct(FunctionExpressionTree $FunctionExpressionTree)
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
}
