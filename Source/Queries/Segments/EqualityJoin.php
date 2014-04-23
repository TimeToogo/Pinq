<?php

namespace Pinq\Queries\Segments;

use Pinq\FunctionExpressionTree;

/**
 * Query segment for an equality join base on a key select for the outer values
 * an another for the inner values
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EqualityJoin extends JoinBase
{
    /**
     * The outer key selector function
     *
     * @var FunctionExpressionTree
     */
    private $outerKeyFunction;

    /**
     * The inner key selector function
     *
     * @var FunctionExpressionTree
     */
    private $innerKeyFunction;

    public function __construct($values, $isGroupJoin, FunctionExpressionTree $outerKeyFunction, FunctionExpressionTree $innerKeyFunction, FunctionExpressionTree $joiningFunction)
    {
        parent::__construct($values, $isGroupJoin, $joiningFunction);
        $this->outerKeyFunction = $outerKeyFunction;
        $this->innerKeyFunction = $innerKeyFunction;
    }

    public function getType()
    {
        return self::EQUALITY_JOIN;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkEqualityJoin($this);
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getOuterKeyFunctionExpressionTree()
    {
        return $this->outerKeyFunction;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getInnerKeyFunctionExpressionTree()
    {
        return $this->innerKeyFunction;
    }

    public function update($values, $isGroupJoin, FunctionExpressionTree $outerKeyFunction, FunctionExpressionTree $innerKeyFunction, FunctionExpressionTree $joiningFunction)
    {
        if ($this->values === $values && $this->isGroupJoin === $isGroupJoin && $this->outerKeyFunction === $outerKeyFunction && $this->innerKeyFunction === $innerKeyFunction && $this->joiningFunction === $joiningFunction) {
            return $this;
        }

        return new self(
                $values,
                $isGroupJoin,
                $outerKeyFunction,
                $innerKeyFunction,
                $joiningFunction);
    }
}
