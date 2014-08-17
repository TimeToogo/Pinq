<?php

namespace Pinq\Queries\Operations;

/**
 * Operation query for adding a range of values to the source
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class AddValues extends ValuesOperation
{
    public function getType()
    {
        return self::ADD_VALUES;
    }

    public function traverse(IOperationVisitor $visitor)
    {
        return $visitor->visitAddValues($this);
    }
}
