<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;

/**
 * Interface of the query expression interpreter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IQueryInterpreter
{
    /**
     * Gets the scope interpreter.
     *
     * @return IScopeInterpreter
     */
    public function getScopeInterpreter();

    /**
     * Interprets the supplied query expression.
     *
     * @param O\Expression $expression
     *
     * @return void
     */
    public function interpret(O\Expression $expression);
}
