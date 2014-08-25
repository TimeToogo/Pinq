<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;
use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the structural expression parameter registry.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class StructuralExpressionRegistry extends StructuralExpressionCollectionBase
{
    /**
     * @return StructuralExpressionRegistry
     */
    public static function none()
    {
        return new self(new \SplObjectStorage());
    }

    /**
     * @param IStructuralExpressionProcessor $processor
     *
     * @return ExpressionRegistry
     */
    public function getExpressions(IStructuralExpressionProcessor $processor)
    {
        return parent::getExpressions($processor);
    }

    /**
     * @param IResolvedParameterRegistry $resolvedParameters
     *
     * @return ResolvedStructuralExpressionRegistry
     */
    public function resolve(IResolvedParameterRegistry $resolvedParameters)
    {
        return new ResolvedStructuralExpressionRegistry($this->structuralExpressions, $resolvedParameters);
    }
}