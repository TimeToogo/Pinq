<?php

namespace Pinq\Queries\Operations; 

/**
 * Base class for an operation query using a supplied index
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IndexOperation extends Operation
{
    /**
     * @var mixed
     */
    private $Index;
    
    public function __construct($Index)
    {
        $this->Index = $Index;
    }
    
    final public function GetIndex()
    {
        return $this->Index;
    }
}
