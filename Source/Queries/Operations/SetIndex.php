<?php

namespace Pinq\Queries\Operations; 

/**
 * Operation query for setting a specified index to a value
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SetIndex extends IndexOperation
{
    private $Value;
    
    public function __construct($Index, $Value)
    {
        parent::__construct($Index);
        $this->Value = $Value;
    }

    public function GetType()
    {
        return self::SetIndex;
    }

    public function GetValue()
    {
        return $this->Value;
    }

    public function Traverse(OperationVisitor $Visitor)
    {
        return $Visitor->VisitSetIndex($this);
    }

}
