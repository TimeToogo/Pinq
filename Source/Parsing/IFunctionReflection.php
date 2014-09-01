<?php

namespace Pinq\Parsing;

use Pinq\Expressions as O;

/**
 * Interface for the reflection of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunctionReflection extends ILocatedFunction
{
    /**
     * Gets the function callable.
     *
     * @return callable
     */
    public function getCallable();

    /**
     * Gets the native function reflection.
     *
     * @return \ReflectionFunctionAbstract
     */
    public function getInnerReflection();

    /**
     * Gets the scope of the function.
     *
     * @return IFunctionScope
     */
    public function getScope();

    /**
     * Gets the resolved magic constants/scopes of the function from
     * the supplied declaration structure.
     *
     * @param IFunctionDeclaration $functionDeclaration
     *
     * @return IFunctionMagic
     */
    public function resolveMagic(IFunctionDeclaration $functionDeclaration);

    /**
     * Gets a globally unique hash of the function based on its
     * signature, location and class scope.
     *
     * @return string
     */
    public function getGlobalHash();

    /**
     * Gets an evaluation context for the same context as the reflected function.
     *
     * @param mixed $variableTable
     *
     * @return O\IEvaluationContext
     */
    public function asEvaluationContext(array $variableTable = []);
}
