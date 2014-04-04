<?php

namespace Pinq\Providers;

use \Pinq\Queries;
use \Pinq\FunctionExpressionTree;

abstract class QueryScope implements \Pinq\Providers\IQueryScope
{
    /**
     * @var Queries\IQueryStream
     */
    protected $QueryStream;
    
    public function __construct(Queries\IQueryStream $QueryStream)
    {
        $this->QueryStream = $QueryStream;
    }

    final public function GetQueryStream()
    {
        return $this->QueryStream;
    }
}
