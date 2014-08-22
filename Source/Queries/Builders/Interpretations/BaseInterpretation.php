<?php

namespace Pinq\Queries\Builders\Interpretations;

use Pinq\Parsing;
use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Queries\Builders\Functions\IFunction;

/**
 * Base class for query expression resolving.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class BaseInterpretation
{
    /**
     * @var IFunctionInterpreter
     */
    protected $functionInterpreter;

    public function __construct(IFunctionInterpreter $functionInterpreter)
    {
        $this->functionInterpreter = $functionInterpreter;
    }

    final protected function getFunctionCallableParameter(IFunction $function)
    {
        return $function->getId() . '-callable';
    }

    final protected function getFunctionScopedVariableParameter(IFunction $function, $variableName)
    {
        return $function->getId() . '--' . $variableName;
    }
}
