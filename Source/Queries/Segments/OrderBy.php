<?php

namespace Pinq\Queries\Segments;

use Pinq\FunctionExpressionTree;

/**
 * Query segment for ordering the values with the supplied functions
 * and order directions
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class OrderBy extends Segment
{
    /**
     * @var OrderFunction[]
     */
    private $orderFunctions;

    public function __construct(array $orderFunctions)
    {
        $this->orderFunctions = $orderFunctions;
    }

    public function getType()
    {
        return self::ORDER_BY;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkOrderBy($this);
    }

    /**
     * @return OrderFunction[]
     */
    public function getOrderFunctions()
    {
        return $this->orderFunctions;
    }

    /**
     * @param FunctionExpressionTree $functionExpressionTree
     * @param boolean $isAscending
     */
    public function thenBy(FunctionExpressionTree $functionExpressionTree, $isAscending)
    {
        return new self(
                array_merge($this->orderFunctions, [new OrderFunction($functionExpressionTree, $isAscending)]));
    }

    public function update(array $orderFunctions)
    {
        if ($this->orderFunctions === $orderFunctions) {
            return $this;
        }

        return new self($orderFunctions);
    }
}
