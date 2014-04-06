<?php

namespace Pinq\Queries\Operations; 

use \Pinq\FunctionExpressionTree;

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
