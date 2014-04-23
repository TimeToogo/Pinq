<?php 

namespace Pinq\Queries\Requests;

/**
 * Request query for the minimum projected value in the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Minimum extends ProjectionRequest
{
    public function getType()
    {
        return self::MINIMUM;
    }
    
    public function traverse(RequestVisitor $visitor)
    {
        return $visitor->visitMinimum($this);
    }
}