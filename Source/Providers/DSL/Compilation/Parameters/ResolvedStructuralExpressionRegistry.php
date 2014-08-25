<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Expressions as O;
use Pinq\Queries\IResolvedParameterRegistry;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;
use Pinq\Queries\ResolvedParameterRegistry;

/**
 * Implementation of the resolved structural expression parameter registry.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ResolvedStructuralExpressionRegistry extends StructuralExpressionCollectionBase
{
    public function __construct(
            \SplObjectStorage $structuralExpressions,
            IResolvedParameterRegistry $resolvedParameters
    ) {
        $resolvedStructuralExpresssions = new \SplObjectStorage();
        foreach($structuralExpressions as $processor) {
            $resolvedStructuralExpresssions[$processor] = $structuralExpressions[$processor]->resolve($resolvedParameters);
        }

        parent::__construct($resolvedStructuralExpresssions);
    }

    /**
     * @param IStructuralExpressionProcessor $processor
     *
     * @return ResolvedExpressionRegistry
     */
    public function getExpressions(IStructuralExpressionProcessor $processor)
    {
        return parent::getExpressions($processor);
    }

    /**
     * @return StructuralExpressionRegistry
     */
    public static function none()
    {
        return new self(new \SplObjectStorage(), ResolvedParameterRegistry::none());
    }
}