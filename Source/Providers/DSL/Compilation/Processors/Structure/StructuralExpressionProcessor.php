<?php

namespace Pinq\Providers\DSL\Compilation\Processors\Structure;

use Pinq\Expressions as O;
use Pinq\PinqException;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Providers\DSL\Compilation\Parameters\ResolvedParameterRegistry;
use Pinq\Queries\Functions\IFunction;

/**
 * Base class of the structural expression processor.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class StructuralExpressionProcessor implements IStructuralExpressionProcessor
{
    protected function addParameter(
            Parameters\ParameterCollection $parameters,
            IFunction $function,
            O\Expression $expression,
            Parameters\IParameterHasher $hasher = null
    ) {
        $parameters->add(
                new StructuralExpressionParameter(
                        $expression,
                        $hasher ?: Parameters\ParameterHasher::valueType(),
                        $function)
        );
    }

    protected function getResolvedValue(
            ResolvedParameterRegistry $resolvedParameters,
            O\Expression $expression
    ) {
        foreach ($resolvedParameters->getParameters() as $parameter) {
            if ($parameter instanceof StructuralExpressionParameter
                    && $parameter->getExpression() === $expression
            ) {
                return $resolvedParameters->getValue($parameter);
            }
        }

        throw new PinqException('Could not get structural expression: matching expression was not found.');
    }

    protected function getResolvedValueExpression(
            ResolvedParameterRegistry $resolvedParameters,
            O\Expression $expression
    ) {
        return O\Expression::value($this->getResolvedValue($resolvedParameters, $expression));
    }
}
