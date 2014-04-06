<?php

namespace Pinq\Queries\Requests; 

abstract class IndexRequest extends Request
{
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
