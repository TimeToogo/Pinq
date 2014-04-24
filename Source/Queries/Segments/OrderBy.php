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
     * @var FunctionExpressionTree[]
     */
    private $functionExpressionTrees;

    /**
     * @var bool[]
     */
    private $isAscendingArray;

    public function __construct(array $functionExpressionTrees, array $isAscendingArray)
    {
        if (array_keys($functionExpressionTrees) !== array_keys($isAscendingArray)) {
            throw new \Pinq\PinqException('Cannot construct %s: $functionExpressionTrees and $isAscendingArray keys do not match', __CLASS__);
        }

        $this->functionExpressionTrees = $functionExpressionTrees;
        $this->isAscendingArray = $isAscendingArray;
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
     * @return FunctionExpressionTree[]
     */
    public function getFunctionExpressionTrees()
    {
        return $this->functionExpressionTrees;
    }

    /**
     * @return bool[]
     */
    public function getIsAscendingArray()
    {
        return $this->isAscendingArray;
    }

    /**
     * @param boolean $isAscending
     */
    public function thenBy(FunctionExpressionTree $functionExpressionTree, $isAscending)
    {
        return new self(
                array_merge($this->functionExpressionTrees, [$functionExpressionTree]),
                array_merge($this->isAscendingArray, [$isAscending]));
    }

    public function update(array $functionExpressionTrees, array $isAscendingArray)
    {
        if ($this->functionExpressionTrees === $functionExpressionTrees && $this->isAscendingArray === $isAscendingArray) {
            return $this;
        }

        return new self($functionExpressionTrees, $isAscendingArray);
    }
}
