<?php

namespace Pinq\Queries;

interface IRequestQuery extends IQuery
{
    /**
     * @return IRequest
     */
    public function GetRequest();
}
