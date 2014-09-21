<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Parsing\IFunctionInterpreter;
use Pinq\Parsing\FunctionInterpreter;

/**
 * Implementation of the parameter hasher that returns a
 * unique hash based on a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionSignatureHasher implements IParameterHasher
{
    /**
     * @var IFunctionInterpreter
     */
    protected $functionInterpreter;

    public function __construct(IFunctionInterpreter $functionInterpreter = null)
    {
        $this->functionInterpreter = $functionInterpreter ?: FunctionInterpreter::getDefault();
    }

    public function hash($value)
    {
        return $this->functionInterpreter->getReflection($value)->getGlobalHash();
    }
}
