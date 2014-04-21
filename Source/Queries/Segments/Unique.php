<?php

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving only unique values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
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
