<?php

namespace Pinq\Providers\Memory;

use \Pinq\Queries;

interface IQueryStreamEvaluator
{
    public function SetValues(array $Values);

    public function Evaluate(Queries\IQueryStream $QueryStream);

    public function GetValues();
}
