<?php

namespace Pinq\Queries;

interface IQueryStream
{
    /**
     * @return IQuery[]
     */
    public function GetStream();
}
