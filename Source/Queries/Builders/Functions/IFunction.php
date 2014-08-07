<?php

namespace Pinq\Queries\Builders\Functions;

/**
 * Interface of function parameter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IFunction
{
    const CALLABLE_VALUE     = 0;
    const CLOSURE_EXPRESSION = 1;

    /**
     * Gets the parameter id.
     *
     * @return string
     */
    public function getId();

    /**
     * Gets the function type.
     *
     * @return int
     */
    public function getType();

    /**
     * Gets the function type.
     *
     * @return callable
     */
    public function getCallable();
}
