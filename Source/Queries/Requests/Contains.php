<?php

namespace Pinq\Queries\Requests; 

/**
 * Request query for a boolean of whether the supplied value
 * is contained within the scope
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Contains extends Request
{
    private $Value;
    
    public function __construct($Value)
    {
        $this->Value = $Value;
    }
    
    public function GetType()
    {
        return self::Contains;
    }
    
    public function GetValue()
    {
        return $this->Value;
    }

    public function Traverse(RequestVisitor $Visitor)
    {
        return $Visitor->VisitContains($this);
    }
}
