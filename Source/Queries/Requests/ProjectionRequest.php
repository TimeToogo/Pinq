<?php

namespace Pinq\Queries\Requests; 

use \Pinq\FunctionExpressionTree;

abstract class ProjectionRequest extends Request
{
    /**
     * @var FunctionExpressionTree|null
     */
    private $FunctionExpressionTree;

    public function __construct(FunctionExpressionTree $FunctionExpressionTree = null)
    {
        $this->FunctionExpressionTree = $FunctionExpressionTree;
    }

    /**
     * @return boolean
     */
    public function HasFunctionExpressionTree()
    {
        return $this->FunctionExpressionTree !== null;
    }

    /**
     * @return FunctionExpressionTree|null
     */
    public function GetFunctionExpressionTree()
    {
        return $this->FunctionExpressionTree;
    }
}
