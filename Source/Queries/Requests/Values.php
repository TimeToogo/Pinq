<?php

namespace Pinq\Queries\Requests;

/**
 * Request query for an iterator which will iterate all the values
 * of the current scope.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Values extends Request
{
    const AS_SELF                      = 0;
    const AS_ARRAY                     = 1;
    const AS_ARRAY_COMPATIBLE_ITERATOR = 2;
    const AS_TRUE_ITERATOR             = 3;
    const AS_TRAVERSABLE               = 4;
    const AS_COLLECTION                = 5;

    /**
     * @var int
     */
    private $valuesType;

    public function __construct($valuesType)
    {
        $this->valuesType = $valuesType;
    }

    public function getType()
    {
        return self::VALUES;
    }

    /**
     * @return int
     */
    public function getValuesType()
    {
        return $this->valuesType;
    }

    public function traverse(IRequestVisitor $visitor)
    {
        return $visitor->visitValues($this);
    }
}
