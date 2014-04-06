<?php

namespace Pinq\Queries\Segments;

class Unique extends Segment
{
    public function GetType()
    {
        return self::Unique;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkUnique($this);
    }
    
    public function Update() 
    {
        return $this;
    }
}
