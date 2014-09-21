<?php

namespace Pinq\Analysis;

/**
 * Base interface of a function / method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface ICallable extends ITyped
{
    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the reflection of the function.
     *
     * @return \ReflectionFunctionAbstract
     */
    public function getReflection();

    /**
     * Gets the return type of the function.
     *
     * @return IType
     */
    public function getReturnType();

    /**
     * Gets the return type of the function with the supplied arguments array.
     *
     * @param array $staticArguments The argument values indexed by their position.
     *
     * @return IType
     */
    public function getReturnTypeWithArguments(array $staticArguments);
}
