<?php

namespace Pinq\Queries\Operations;

/**
 * Operation query for unsetting a value at the specified index
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class UnsetIndex extends IndexOperation
{
    public function getType()
    {
        return self::UNSET_INDEX;
    }

    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitUnsetIndex($this);
    }
}
