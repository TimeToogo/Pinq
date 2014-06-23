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
     * @var FunctionExpressionTree|null
     */
    private $onFunction;

    public function __construct($values, $isGroupJoin, FunctionExpressionTree $onFunction = null, FunctionExpressionTree $joiningFunction)
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
     * @return boolean
     */
    public function hasOnFunctionExpressionTree()
    {
        return $this->onFunction !== null;
    }

    /**
     * @return FunctionExpressionTree|null
     */
    public function getOnFunctionExpressionTree()
    {
        return $this->onFunction;
    }

    public function update($values, $isGroupJoin, FunctionExpressionTree $onFunction = null, FunctionExpressionTree $joiningFunction)
    {
        if ($this->values === $values && $this->isGroupJoin === $isGroupJoin && $this->onFunction === $onFunction && ($this->joiningFunction = $joiningFunction)) {
            return $this;
        }

        return new self($values, $isGroupJoin, $onFunction, $joiningFunction);
    }
}
