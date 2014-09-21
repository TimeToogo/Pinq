<?php

namespace Pinq\Analysis;

/**
 * Interface of a function.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunction extends ICallable
{
    /**
     * Gets the function name.
     *
     * @return string
     */
    public function getName();

    /**
     * Gets the reflection of the function.
     *
     * @return \ReflectionFunction
     */
    public function getReflection();
}
