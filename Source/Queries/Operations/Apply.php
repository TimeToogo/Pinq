<?php

namespace Pinq\Queries\Operations; 

use \Pinq\FunctionExpressionTree;

class Apply extends ExpressionOperation
{
    public function GetType()
    {
        return self::Apply;
    }

    public function Traverse(OperationVisitor $Visitor)
    {
        return $Visitor->VisitApply($this);
    }

}
