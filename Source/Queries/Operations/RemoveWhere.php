<?php

namespace Pinq\Queries\Operations;

use Pinq\Queries\Functions;

/**
 * Operation query for removing values that satisfy the supplied function
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class RemoveWhere extends Operation
{
    /**
     * @var Functions\ElementProjection
     */
    private $predicateFunction;

    public function __construct(Functions\ElementProjection $predicateFunction)
    {
        $this->predicateFunction = $predicateFunction;
    }

    /**
     * @return Functions\ElementProjection
     */
    public function getPredicateFunction()
    {
        return $this->predicateFunction;
    }

    public function getType()
    {
        return self::REMOVE_WHERE;
    }

    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitRemoveWhere($this);
    }
}
