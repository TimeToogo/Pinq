<?php

namespace Pinq\Queries\Common\Source;

/**
 * Array/iterator value source.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ArrayOrIterator extends Base
{
    /**
     * @var string
     */
    private $parameterId;

    public function __construct($parameterId)
    {
        $this->parameterId = $parameterId;
    }

    public function getType()
    {
        return self::ARRAY_OR_ITERATOR;
    }

    /**
     * Gets the parameter id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->parameterId;
    }
}
