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
     * @var \Traversable|array
     */
    private $Values;

    public function __construct($OperationType, $Values)
    {
        if(!($Values instanceof \Traversable) && !is_array($Values)) {
            throw new \Pinq\PinqException(
                    'Invalid argument $values for %s: expecting \Traversable or array, %s given',
                    __METHOD__,
                    \Pinq\Utilities::GetTypeOrClass($Values));
        }
        if(!self::IsValid($OperationType)) {
            throw new \Pinq\PinqException('Invalid operation type');
        }
        $this->OperationType = $OperationType;
        $this->Values = $Values;
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
     * @return \Traversable|array
     */
    public function GetTraversable()
    {
        return $this->Values;
    }
}
