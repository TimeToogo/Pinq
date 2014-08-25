<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\Preprocessors;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;

/**
 * Inlines variable variables as structural parameters
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralVariableProcessor implements IStructuralExpressionProcessor
{
    public function matches(O\Expression $expression)
    {
        return $expression instanceof O\VariableExpression && !($expression->getName() instanceof O\ValueExpression);
    }

    public function parameterize(
            O\Expression $expression,
            Parameters\ExpressionCollectionContext $expressionCollection
    ) {
        /** @var $expression O\VariableExpression */
        $expressionCollection->add($expression->getName());
    }

    public function hash(O\Expression $expression, Parameters\ResolvedExpressionRegistry $expressionRegistry)
    {
        return $expressionRegistry->evaluate($expression);
    }

    public function inline(O\Expression $expression, Parameters\ResolvedExpressionRegistry $expressionRegistry)
    {
        /** @var $expression O\VariableExpression */
        return $expression->update(O\Expression::value($expressionRegistry->evaluate($expression->getName())));
    }
}