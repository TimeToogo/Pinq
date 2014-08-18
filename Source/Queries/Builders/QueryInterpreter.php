<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;

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

    public function __construct(
            $idPrefix,
            IScopeInterpreter $scopeInterpreter,
            O\IEvaluationContext $evaluationContext = null
    ) {
        parent::__construct($idPrefix, $evaluationContext);
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
