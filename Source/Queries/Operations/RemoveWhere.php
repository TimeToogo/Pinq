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

    public function getParameters()
    {
        return $this->predicateFunction->getParameterIds();
    }

    public function getType()
    {
        return self::REMOVE_WHERE;
    }

    public function traverse(IOperationVisitor $visitor)
    {
        return $visitor->visitRemoveWhere($this);
    }

    /**
     * @param Functions\ElementProjection $predicateFunction
     *
     * @return RemoveWhere
     */
    public function update(Functions\ElementProjection $predicateFunction)
    {
        if ($this->predicateFunction === $predicateFunction) {
            return $this;
        }

        return new self($predicateFunction);
    }
}
