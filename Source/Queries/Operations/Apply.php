<?php

namespace Pinq\Queries\Operations;

/**
 * Operation query for applying the supplied function
 * to the source
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Apply extends MutatorOperation
{
    public function getType()
    {
        return self::APPLY;
    }

    public function traverse(IOperationVisitor $visitor)
    {
        return $visitor->visitApply($this);
    }
}
