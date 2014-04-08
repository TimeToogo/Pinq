<?php

namespace Pinq\Queries\Segments; 

class Operation extends Segment
{
    const Union = 1;
    const Intersect = 2;
    const Difference = 3;
    
    const Append = 4;
    const WhereIn = 5;
    const Except = 6;

    /**
     * @var int
     */
    private $OperationType;

    /**
     * @var \Pinq\ITraversable
     */
    private $Traversable;

    public function __construct($OperationType, \Pinq\ITraversable $Traversable)
    {
        if(!self::IsValid($OperationType)) {
            throw new \Pinq\PinqException('Invalid operation type');
        }
        $this->OperationType = $OperationType;
        $this->Traversable = $Traversable;
    }

    final public static function IsValid($OperationType)
    {
        return in_array($OperationType, [self::Union, self::Intersect, self::Difference, self::Append, self::WhereIn, self::Except]);
    }

    public function GetType()
    {
        return self::Operate;
    }

    public function Traverse(SegmentWalker $Walker)
    {
        return $Walker->WalkOperation($this);
    }

    /**
     * @return int
     */
    public function GetOperationType()
    {
        return $this->OperationType;
    }

    /**
     * @return \Pinq\ITraversable
     */
    public function GetTraversable()
    {
        return $this->Traversable;
    }
}
