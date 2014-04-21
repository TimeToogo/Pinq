<?php

namespace Pinq\Queries\Requests; 

use \Pinq\FunctionExpressionTree;

/**
 * Request query for a custom aggregate using the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Aggregate extends Request
{
    /**
     * @var FunctionExpressionTree
     */
    private $FunctionExpressionTree;

    public function __construct(FunctionExpressionTree $FunctionExpressionTree)
    {
        $this->FunctionExpressionTree = $FunctionExpressionTree;
    }
    
    public function GetType()
    {
        return self::Aggregate;
    }

    /**
     * @return FunctionExpressionTree
     */
    public function GetFunctionExpressionTree()
    {
        return $this->FunctionExpressionTree;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitAggregate($this);
    }

}
