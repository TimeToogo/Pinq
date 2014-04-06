<?php

namespace Pinq\Queries\Operations; 

abstract class IndexOperation extends Operation
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
