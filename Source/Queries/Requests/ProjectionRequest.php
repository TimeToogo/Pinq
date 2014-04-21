<?php

namespace Pinq\Queries\Requests; 

use \Pinq\FunctionExpressionTree;

/**
 * Base class for a request which optionally projects the values with
 * the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
