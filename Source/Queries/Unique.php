<?php

namespace Pinq\Queries;

class Unique implements IQuery
{
    public function GetType()
    {
        return self::Unique;
    }

    public function Traverse(QueryStreamWalker $Walker)
    {
        return $Walker->WalkUnique($this);
    }
    
    public function Update() 
    {
        return $this;
    }
}
