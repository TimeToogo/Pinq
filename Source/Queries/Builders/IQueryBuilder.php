<?php
namespace Pinq\Queries\Builders;

use Pinq\Parsing\IFunctionInterpreter;

interface IQueryBuilder
{
    /**
     * Gets the function interpreter.
     *
     * @return IFunctionInterpreter
     */
    public function getFunctionInterpreter();
}
