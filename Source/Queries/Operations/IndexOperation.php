<?php

namespace Pinq\Queries\Operations;

/**
 * Base class for an operation query using a supplied index
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class IndexOperation extends Operation
{
    /**
     * @var string
     */
    protected $indexParameterId;

    public function __construct($indexParameterId)
    {
        $this->indexParameterId = $indexParameterId;
    }

    /**
     * Gets the index parameter id.
     *
     * @return string
     */
    final public function getIndexId()
    {
        return $this->indexParameterId;
    }
}
