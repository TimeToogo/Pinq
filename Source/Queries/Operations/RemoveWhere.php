<?php 

namespace Pinq\Queries\Operations;

/**
 * Operation query for removing values that satisfy the supplied function
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RemoveWhere extends ExpressionOperation
{
    public function getType()
    {
        return self::REMOVE_WHERE;
    }
    
    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitRemoveWhere($this);
    }
}