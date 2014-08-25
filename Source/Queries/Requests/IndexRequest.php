<?php

namespace Pinq\Queries\Requests;

/**
 * Base class for a request with a specified index
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class IndexRequest extends Request
{
    /**
     * @var string
     */
    private $indexId;

    public function __construct($indexId)
    {
        $this->indexId = $indexId;
    }

    public function getParameters()
    {
        return [$this->indexId];
    }

    final public function getIndexId()
    {
        return $this->indexId;
    }
}
