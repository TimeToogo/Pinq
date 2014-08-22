<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;

/**
 * Implementation of the scope builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeBuilder extends BaseQueryBuilder implements IScopeBuilder
{
    public function buildScopeInterpreter(
            Interpretations\IScopeInterpretation $interpretation,
            O\IEvaluationContext $evaluationContext = null
    ) {
        return new ScopeInterpreter($interpretation, $evaluationContext);
    }

    public function buildScopeParser()
    {
        return new Interpretations\ScopeParser($this->functionInterpreter);
    }

    public function buildScopeResolver()
    {
        return new Interpretations\ScopeResolver($this->functionInterpreter);
    }
}
