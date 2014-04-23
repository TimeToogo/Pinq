<?php 

namespace Pinq\Queries\Operations;

/**
 * Base class for an operation query for with a range of values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ValuesOperation extends Operation
{
    private $values;
    
    public function __construct($values)
    {
        $this->values = $values;
    }
    
    public function getValues()
    {
        return $this->values;
    }
}