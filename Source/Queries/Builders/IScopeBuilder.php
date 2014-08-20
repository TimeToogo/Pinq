<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;

/**
 * Implementation of the scope builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeBuilder extends IQueryBuilder
{
    /**
     * Builds a scope parser interpretation.
     *
     * @return Interpretations\IScopeParser
     */
    public function buildScopeParser();

    /**
     * Builds a scope resolver interpretation.
     *
     * @return Interpretations\IScopeResolver
     */
    public function buildScopeResolver();

    /**
     * Builds a scope interpreter with the supplied interpretation.
     *
     * @param Interpretations\IScopeInterpretation $interpretation
     * @param O\IEvaluationContext                 $evaluationContext
     *
     * @return IScopeInterpreter
     */
    public function buildScopeInterpreter(
            Interpretations\IScopeInterpretation $interpretation,
            O\IEvaluationContext $evaluationContext = null
    );
}
