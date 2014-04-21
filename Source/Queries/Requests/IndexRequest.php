<?php

namespace Pinq\Queries\Requests; 

/**
 * Base class for a request with a specified index
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IndexRequest extends Request
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
