<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries;
use Pinq\Queries\Builders\Interpretations\IJoinOptionsInterpretation;

/**
 * Interface for join options interpreters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IJoinOptionsInterpreter
{
    /**
     * Gets the join options interpretation.
     *
     * @return IJoinOptionsInterpretation
     */
    public function getInterpretation();

    /**
     * Interprets the supplied join options expression.
     *
     * @param O\MethodCallExpression $expression
     * @param O\MethodCallExpression $sourceExpression
     *
     * @return void
     */
    public function interpretJoinOptions(
            O\MethodCallExpression $expression,
            O\MethodCallExpression &$sourceExpression = null
    );
}
