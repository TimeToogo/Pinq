<?php

namespace Pinq\Queries\Operations;

/**
 * Operation query for clearing all values from the source
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Clear extends Operation
{
    public function getType()
    {
        return self::CLEAR;
    }

    public function traverse(IOperationVisitor $visitor)
    {
        return $visitor->visitClear($this);
    }
}
