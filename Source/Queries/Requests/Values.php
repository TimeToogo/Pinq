<?php 

namespace Pinq\Queries\Requests;

/**
 * Request query for an iterator which will iterate all the values
 * of the current scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Values extends Request
{
    public function getType()
    {
        return self::VALUES;
    }
    
    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitValues($this);
    }
}