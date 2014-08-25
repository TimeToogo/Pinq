<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the expression parameter registry.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionRegistry extends ExpressionCollectionBase
{
    public static function none()
    {
        return new self(new \SplObjectStorage());
    }

    /**
     * Builds a resolved expression registry with the supplied parameter registry.
     *
     * @param IResolvedParameterRegistry $resolvedParameters
     *
     * @return ResolvedExpressionRegistry
     */
    public function resolve(IResolvedParameterRegistry $resolvedParameters)
    {
        return new ResolvedExpressionRegistry($this->expressions, $resolvedParameters);
    }
}