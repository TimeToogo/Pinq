<?php

namespace Pinq\Queries\Builders;

/**
 * Implementation of the scope builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ScopeBuilder extends BaseQueryBuilder implements IScopeBuilder
{
    public function buildScopeInterpreter(Interpretations\IScopeInterpretation $interpretation, $closureScopeType = null)
    {
        return new ScopeInterpreter($interpretation, $closureScopeType);
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