<?php

namespace Pinq\Queries;

class QueryStream implements IQueryStream
{
    /**
     * @var IQuery[]
     */
    private $Stream = [];

    public function __construct(array $Stream)
    {
        $this->Stream = $Stream;
    }

    /**
     * @return IQuery[]
     */
    public function GetStream()
    {
        return $this->Stream;
    }
}
