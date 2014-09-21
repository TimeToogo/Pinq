<?php

namespace Pinq\Analysis;

/**
 * Interface of a method.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IMethod extends ICallable, ITypeOperation
{
    /**
     * Gets the name of the method.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the reflection of the method.
     *
     * @return \ReflectionMethod
     */
    public function getReflection();
}
