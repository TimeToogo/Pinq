<?php 

namespace Pinq\Queries\Operations;

/**
 * Operation query for removing a range of values to the source
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class RemoveValues extends ValuesOperation
{
    public function getType()
    {
        return self::REMOVE_VALUES;
    }
    
    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitRemoveValues($this);
    }
}