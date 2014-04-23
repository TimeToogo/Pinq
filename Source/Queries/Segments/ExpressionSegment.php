<?php

namespace Pinq\Queries\Segments;

use Pinq\FunctionExpressionTree;

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
    private $functionExpressionTree;

    final public function __construct(FunctionExpressionTree $functionExpressionTree)
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

    public function update(\Pinq\FunctionExpressionTree $functionExpressionTree)
    {
        if ($this->functionExpressionTree === $functionExpressionTree) {
            return $this;
        }

        return new static($functionExpressionTree);
    }
}
