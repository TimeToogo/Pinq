<?php 

namespace Pinq\Queries\Segments;

/**
 * Query segment for retrieving only unique values
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Unique extends Segment
{
    public function getType()
    {
        return self::UNIQUE;
    }
    
    public function traverse(SegmentWalker $walker)
    {
        return $walker->walkUnique($this);
    }
    
    public function update()
    {
        return $this;
    }
}