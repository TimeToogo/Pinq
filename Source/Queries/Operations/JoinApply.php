<?php

namespace Pinq\Queries\Operations;

use Pinq\FunctionExpressionTree;
use Pinq\Queries\Common\Join;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoinApply extends Join\Base implements \Pinq\Queries\IOperation
{
    /**
     * The function for selecting the resulting values of the join
     *
     * @var FunctionExpressionTree
     */
    protected $applyFunction;

    public function __construct($values, $isGroupJoin, Join\IFilter $filter = null, FunctionExpressionTree $applyFunction)
    {
        parent::__construct($values, $isGroupJoin, $filter);
        
        $this->applyFunction = $applyFunction;
    }
    
    public function getType()
    {
        return self::JOIN_APPLY;
    }
    
    /**
     * @return FunctionExpressionTree
     */
    final public function getApplyFunctionExpressionTree()
    {
        return $this->applyFunction;
    }
    
    public function traverse(OperationVisitor $visitor)
    {
        $visitor->visitJoinApply($this);
    }
}
