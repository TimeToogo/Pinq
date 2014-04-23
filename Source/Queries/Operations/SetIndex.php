<?php

namespace Pinq\Queries\Operations;

/**
 * Operation query for setting a specified index to a value
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class SetIndex extends IndexOperation
{
    private $value;

    public function __construct($index, $value)
    {
        parent::__construct($index);
        $this->value = $value;
    }

    public function getType()
    {
        return self::SET_INDEX;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function traverse(OperationVisitor $visitor)
    {
        return $visitor->visitSetIndex($this);
    }
}
