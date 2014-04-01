<?php

namespace Pinq\Queries\Expression;

use \Pinq\Expressions\Expression;

class OrderBy extends Query
{
    /**
     * @var Expression[]
     */
    private $Expressions;

    /**
     * @var bool[]
     */
    private $IsAscendingArray;

    public function __construct(array $Expressions, array $IsAscendingArray)
    {
        if (array_keys($Expressions) !== array_keys($IsAscendingArray)) {
            throw new \Pinq\PinqException('Cannot construct order by: expressions array and is asceding array keys do not match');
        }

        $this->Expressions = $Expressions;
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
        return $this->Expressions;
    }

    /**
     * @return bool[]
     */
    public function GetIsAscendingArray()
    {
        return $this->IsAscendingArray;
    }
}
