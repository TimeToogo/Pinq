<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Parsing\IFunctionInterpreter;

/**
 * Factory class for the parameter hasher implementations.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterHasher
{
    private function __construct()
    {

    }

    /**
     * @return ValueTypeHasher
     */
    public static function valueType()
    {
        return new ValueTypeHasher();
    }

    /**
     * @param IFunctionInterpreter|null $functionInterpreter
     *
     * @return FunctionSignatureHasher
     */
    public static function functionSignature(IFunctionInterpreter $functionInterpreter = null)
    {
        return new FunctionSignatureHasher($functionInterpreter);
    }

    /**
     * @param O\IEvaluationContext $evaluationContext
     *
     * @return CompiledRequestQueryHasher
     */
    public static function compiledRequestQuery(O\IEvaluationContext $evaluationContext = null)
    {
        return new CompiledRequestQueryHasher($evaluationContext);
    }
}
