<?php

namespace Pinq\Queries\Operations;

use Pinq\Queries\IOperation;

/**
 * The operation visitor is a utility class that will visit any
 * operation in a respective method.
 * This is used by the repository providers to as to execute
 * the supplied operation query
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class OperationVisitor
{
    /**
     * @param mixed The returned value
     */
    final public function visit(IOperation $operation)
    {
        return $operation->traverse($this);
    }

    public function visitApply(Apply $operation)
    {

    }

    public function visitJoinApply(JoinApply $operation)
    {

    }

    public function visitAddValues(AddValues $operation)
    {

    }

    public function visitRemoveValues(RemoveValues $operation)
    {

    }

    public function visitRemoveWhere(RemoveWhere $operation)
    {

    }

    public function visitClear(Clear $operation)
    {

    }

    public function visitUnsetIndex(UnsetIndex $operation)
    {

    }

    public function visitSetIndex(SetIndex $operation)
    {

    }
}
