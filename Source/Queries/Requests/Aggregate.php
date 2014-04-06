<?php

namespace Pinq\Queries\Requests; 

use \Pinq\FunctionExpressionTree;

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
