<?php

namespace Pinq\Queries;

class Scope implements IScope
{
    /**
     * @var ISegment[]
     */
    private $Segments = [];

    public function __construct(array $Segments)
    {
        $this->Segments = $Segments;
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->Segments);
    }
    
    /**
     * @return ISegment[]
     */
    public function GetSegments()
    {
        return $this->Segments;
    }
    
    public function IsEmpty()
    {
        return empty($this->Segments);
    }
    
    public function Append(ISegment $Query)
    {
        return new self(array_merge($this->Segments, [$Query]));
    }
    
    public function Update(array $Segments)
    {
        if($this->Segments === $Segments) {
            return $this;
        }
        
        return new self($Segments);
    }
    
    public function UpdateLast(ISegment $Segment)
    {
        if(end($this->Segments) === $Segment) {
            return $this;
        }
        
        return $this->Append($Segment);
    }
}
