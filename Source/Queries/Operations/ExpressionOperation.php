<?php

namespace Pinq\Queries\Operations;

use Pinq\FunctionExpressionTree;

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
    private $functionExpressionTree;

    public function __construct(FunctionExpressionTree $functionExpressionTree)
    {
        $this->functionExpressionTree = $functionExpressionTree;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getFunctionExpressionTree()
    {
        return $this->functionExpressionTree;
    }
}
