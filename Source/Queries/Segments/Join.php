<?php

namespace Pinq\Queries\Segments;

use Pinq\FunctionExpressionTree;

/**
 * Query segment for a join filtered by the supplied function
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Join extends JoinBase
{
    /**
     * The join filter expression tree
     *
     * @var FunctionExpressionTree
     */
    private $onFunction;

    public function __construct($values, $isGroupJoin, FunctionExpressionTree $onFunction, FunctionExpressionTree $joiningFunction)
    {
        parent::__construct($values, $isGroupJoin, $joiningFunction);
        $this->onFunction = $onFunction;
    }

    public function getType()
    {
        return self::JOIN;
    }

    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkJoin($this);
    }

    /**
     * @return FunctionExpressionTree
     */
    public function getOnFunctionExpressionTree()
    {
        return $this->onFunction;
    }

    public function update($values, $isGroupJoin, FunctionExpressionTree $onFunction, FunctionExpressionTree $joiningFunction)
    {
        if ($this->values === $values && $this->isGroupJoin === $isGroupJoin && $this->onFunction === $onFunction && ($this->joiningFunction = $joiningFunction)) {
            return $this;
        }

        return new self($values, $isGroupJoin, $onFunction, $joiningFunction);
    }
}
