<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Expression;

use Pinq\Queries;
use Pinq\Queries\Functions\IFunction;

/**
 * Interface of the expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IExpressionProcessor
{
    /**
     * Processes/updates the supplied function's expression.
     *
     * @param IFunction $function
     *
     * @return IFunction
     */
    public function processFunction(IFunction $function);
}
