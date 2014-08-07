<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\Queries\Builders\Interpretations\IScopeInterpretation;

/**
 * Base class for query interpreters.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryInterpreter extends ExpressionInterpreter implements IQueryInterpreter
{
    /**
     * @var IScopeInterpreter
     */
    protected $scopeInterpreter;

    public function __construct($idPrefix, IScopeInterpreter $scopeInterpreter, $closureScopeType = null)
    {
        parent::__construct($idPrefix, $closureScopeType);
        $this->scopeInterpreter = $scopeInterpreter;
    }

    public function getScopeInterpreter()
    {
        return $this->getScopeInterpreter();
    }

    final protected function interpretSourceAsScope(O\TraversalExpression $expression)
    {
        $this->scopeInterpreter->interpretScope($expression->getValue());
    }
}
