<?php

namespace Pinq\Providers\DSL\Compilation\Parameters;

use Pinq\Queries\IResolvedParameterRegistry;

/**
 * Implementation of the expression parameter registry.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ParameterRegistry extends ParameterCollectionBase
{
    public static function none()
    {
        return new self([]);
    }

    /**
     * Builds a resolved expression registry with the supplied parameter registry.
     *
     * @param IResolvedParameterRegistry $resolvedParameters
     *
     * @return ResolvedParameterRegistry
     */
    public function resolve(IResolvedParameterRegistry $resolvedParameters)
    {
        return new ResolvedParameterRegistry($this->parameters, $resolvedParameters);
    }
}
