<?php

namespace Pinq\Queries\Operations; 

abstract class ValuesOperation extends Operation
{
    private $Values;
    
    public function __construct($Values)
    {
        $this->Values = $Values;
    }
    
    public function GetValues()
    {
        return $this->Values;
    }
}
