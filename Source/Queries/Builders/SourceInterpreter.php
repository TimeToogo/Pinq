<?php

namespace Pinq\Queries\Builders;

use Pinq\Expressions as O;
use Pinq\IQueryable;
use Pinq\Queries\Builders\Interpretations\ISourceInterpretation;
use Pinq\Queries;

/**
 * Implementation of the source expression interpreter.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class SourceInterpreter extends ExpressionInterpreter implements ISourceInterpreter
{
    /**
     * @var string
     */
    protected $segmentId;

    /**
     * @var ISourceInterpretation
     */
    protected $interpretation;

    /**
     * @var IScopeInterpreter
     */
    protected $scopeInterpreter;

    public function __construct(
            $segmentId,
            ISourceInterpretation $interpretation,
            IScopeInterpreter $scopeInterpreter,
            O\IEvaluationContext $evaluationContext = null
    ) {
        parent::__construct($segmentId, $evaluationContext);
        $this->interpretation   = $interpretation;
        $this->scopeInterpreter = $scopeInterpreter;
    }

    public function getInterpretation()
    {
        return $this->interpretation;
    }

    public function interpretSource(O\Expression $expression)
    {
        $isQueryScope = false;
        $queryableQueryResolver = new O\DynamicExpressionWalker([
                O\TraversalExpression::getType() =>
                        function (O\TraversalExpression $expression, O\ExpressionWalker $self) use (&$isQueryScope) {
                            $expression = $expression->updateValue($self->walk($expression->getValue()));

                            if ($isQueryScope) {
                                return $expression;
                            } else {
                                return $self->walk(O\Expression::value($expression->evaluate($this->evaluationContext)));
                            }
                        },
                O\ValueExpression::getType() =>
                        function (O\ValueExpression $expression) use (&$isQueryScope) {
                            if ($expression->getValue() instanceof IQueryable) {
                                $isQueryScope = true;
                            }

                            return $expression;
                        }
        ]);

        $expression = $queryableQueryResolver->walk($expression);

        if ($isQueryScope) {
            $this->scopeInterpreter->interpretScope($expression);
            $this->interpretation->interpretQueryScope($this->getId('source-scope'), $this->scopeInterpreter->getInterpretation());
        } else {
            $this->interpretation->interpretArrayOrIterator($this->getId('source-iterator'), $expression->evaluate($this->evaluationContext));
        }
    }
}
