<?php

namespace Pinq\Queries\Operations; 

/**
 * Base class for an operation query for with a range of values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
