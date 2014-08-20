<?php

namespace Pinq\Queries\Operations;

/**
 * Interface of the operation visitor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationVisitor
{
    public function visitUnsetIndex(UnsetIndex $operation);

    public function visitApply(Apply $operation);

    public function visitClear(Clear $operation);

    public function visitSetIndex(SetIndex $operation);

    public function visitAddValues(AddValues $operation);

    public function visitRemoveWhere(RemoveWhere $operation);

    public function visitJoinApply(JoinApply $operation);

    public function visitRemoveValues(RemoveValues $operation);
}
