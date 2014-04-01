<?php

namespace Pinq\Queries;

class Operation implements IQuery
{
    const Union = 1;
    const Append = 2;
    const Intersect = 3;
    const Except = 4;

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
        $this->OperationType = $OperationType;
        $this->Traversable = $Traversable;
    }

    final public static function IsValid($OperationType)
    {
        return in_array($OperationType, [self::Union, self::Append, self::Intersect, self::Except]);
    }

    public function GetType()
    {
        return self::Operate;
    }

    public function Traverse(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitOperation($this);
    }

    /**
     * @return IQueryable
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
