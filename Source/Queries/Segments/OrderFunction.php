<?php

namespace Pinq\Queries\Segments;

use Pinq\FunctionExpressionTree;

/**
 * Container for a function expression tree assiociated with an order direction
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OrderFunction
{
    /**
     * @var FunctionExpressionTree
     */
    private $functionExpressionTree;

    /**
     * @var bool
     */
    private $isAscending;

    public function __construct(FunctionExpressionTree $functionExpressionTree, $isAscending)
    {
        $this->functionExpressionTree = $functionExpressionTree;
        $this->isAscending = $isAscending;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getFunctionExpressionTree()
    {
        return $this->functionExpressionTree;
    }

    /**
     * @return bool
     */
    public function isAscending()
    {
        return $this->isAscending;
    }
}
