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
    private $indexParameter;

    public function __construct($indexParameter)
    {
        $this->indexParameter = $indexParameter;
    }

    final public function getIndexParameter()
    {
        return $this->indexParameter;
    }
}
