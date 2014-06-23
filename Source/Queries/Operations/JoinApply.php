<?php

namespace Pinq\Queries\Operations;

use Pinq\FunctionExpressionTree;
use Pinq\Queries\Segments\Join;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinApply extends JoinApplyBase
{
   /**
     * The join filter expression tree
     *
     * @var FunctionExpressionTree|null
     */
    private $onFunction;

    public function __construct($values, $isGroupJoin, FunctionExpressionTree $onFunction = null, FunctionExpressionTree $applyFunction)
    {
        parent::__construct($values, $isGroupJoin, $applyFunction);
        $this->onFunction = $onFunction;
    }
    
    public function getType()
    {
        return self::JOIN_APPLY;
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
    
    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitJoinApply($this);
    }
}
