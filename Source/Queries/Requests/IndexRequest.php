<?php

namespace Pinq\Queries\Requests;

/**
 * Base class for a request with a specified index
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class IndexRequest extends Request
{
    /**
     * @var mixed
     */
    private $index;

    public function __construct($index)
    {
        $this->index = $index;
    }

    final public function getIndex()
    {
        return $this->index;
    }
}
