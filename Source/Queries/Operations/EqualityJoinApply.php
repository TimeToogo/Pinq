<?php

namespace Pinq\Queries\Operations;

use Pinq\FunctionExpressionTree;
use Pinq\Queries\Segments\EqualityJoin;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class EqualityJoinApply extends JoinApplyBase
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

    public function __construct($values, $isGroupJoin, FunctionExpressionTree $outerKeyFunction, FunctionExpressionTree $innerKeyFunction, FunctionExpressionTree $applyFunction)
    {
        parent::__construct($values, $isGroupJoin, $applyFunction);
        $this->outerKeyFunction = $outerKeyFunction;
        $this->innerKeyFunction = $innerKeyFunction;
    }
    
    public function getType()
    {
        return self::EQUALITY_JOIN_APPLY;
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

    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitEqualityJoinApply($this);
    }
}
