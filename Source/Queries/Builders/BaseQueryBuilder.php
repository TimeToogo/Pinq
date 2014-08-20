<?php

namespace Pinq\Queries\Builders;

use Pinq\Parsing\IFunctionInterpreter;

/**
 * Base class of the query builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class BaseQueryBuilder implements IQueryBuilder
{
    /**
     * @var IFunctionInterpreter
     */
    protected $functionInterpreter;

    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        $this->functionInterpreter = $functionInterpreter;
    }

    public function getFunctionInterpreter()
    {
        return $this->functionInterpreter;
    }
}
