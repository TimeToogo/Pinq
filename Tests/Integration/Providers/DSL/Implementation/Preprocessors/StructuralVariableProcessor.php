<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\Preprocessors;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Providers\DSL\Compilation\Processors\Structure\StructuralExpressionProcessor;
use Pinq\Queries\Functions\FunctionBase;

/**
 * Inlines variable variables as structural parameters
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralVariableProcessor extends StructuralExpressionProcessor
{
    public function matches(
            FunctionBase $function,
            O\Expression $expression
    ) {
        return $expression instanceof O\VariableExpression && !($expression->getName() instanceof O\ValueExpression);
    }

    public function parameterize(
            FunctionBase $function,
            O\Expression $expression,
            Parameters\ParameterCollection $parameters
    ) {
        /** @var $expression O\VariableExpression */
        $this->addParameter($parameters, $function, $expression->getName());
    }

    public function inline(
            FunctionBase $function,
            O\Expression $expression,
            Parameters\ResolvedParameterRegistry $parameters
    ) {
        /** @var $expression O\VariableExpression */
        return $expression->update($this->getResolvedValueExpression($parameters, $expression->getName()));
    }
}