<?php

namespace Pinq\Queries\Functional;

class OrderBy extends Query
{
    /**
     * @var callable[]
     */
    private $Functions;

    /**
     * @var bool[]
     */
    private $IsAscendingArray;

    public function __construct(array $Functions, array $IsAscendingArray)
    {
        if (array_keys($Functions) !== array_keys($IsAscendingArray)) {
            throw new \Pinq\PinqException('Cannot construct order by: functions array and is asceding array keys do not match');
        }

        $this->Functions = $Functions;
        $this->IsAscendingArray = $IsAscendingArray;
    }

    public function GetType()
    {
        return self::OrderBy;
    }

    public function TraverseQuery(QueryStreamVisitor $Visitor)
    {
        $Visitor->VisitOrderBy($this);
    }

    /**
     * @return callable[]
     */
    public function GetFunctions()
    {
        return $this->Functions;
    }

    /**
     * @return bool[]
     */
    public function GetIsAscendingArray()
    {
        return $this->IsAscendingArray;
    }
}
