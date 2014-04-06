<?php

namespace Pinq\Queries\Requests; 

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
