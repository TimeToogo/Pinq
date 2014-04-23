<?php 

namespace Pinq\Queries\Operations;

/**
 * Operation query for clearing all values from the source
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Clear extends Operation
{
    public function getType()
    {
        return self::CLEAR;
    }
    
    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitClear($this);
    }
}