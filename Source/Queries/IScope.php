<?php

namespace Pinq\Queries;

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
