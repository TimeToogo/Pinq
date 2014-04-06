<?php

namespace Pinq\Queries;

interface IQuery
{
    /**
     * @return IScope
     */
    public function GetScope();
}
