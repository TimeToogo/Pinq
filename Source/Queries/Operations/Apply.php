<?php

namespace Pinq\Queries\Operations;

use Pinq\FunctionExpressionTree;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Apply extends ExpressionOperation
{
    public function getType()
    {
        return self::APPLY;
    }

    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitApply($this);
    }
}
