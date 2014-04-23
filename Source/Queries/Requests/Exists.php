<?php 

namespace Pinq\Queries\Requests;

/**
 * Request query for a boolean of whether any values are 
 * contained in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Exists extends Request
{
    public function getType()
    {
        return self::EXISTS;
    }
    
    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitExists($this);
    }
}