<?php 

namespace Pinq\Queries\Operations;

/**
 * Operation query for adding a range of values to the source
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class AddValues extends ValuesOperation
{
    public function getType()
    {
        return self::ADD_VALUES;
    }
    
    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitAddValues($this);
    }
}