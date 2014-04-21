<?php

namespace Pinq\Queries;

/**
 * The query scope. This contains many query segments which
 * in order represent the scope of the query to be loaded/executed.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IScope extends \IteratorAggregate
{
    /**
     * @return ISegment[]
     */
    public function GetSegments();
    
    /**
     * @return boolean
     */
    public function IsEmpty();
    
    /**
     * @return IScope
     */
    public function Append(ISegment $Segment);
    
    /**
     * @return IScope
     */
    public function Update(array $Segments);
    
    /**
     * @return IScope
     */
    public function UpdateLast(ISegment $Segment);
}
