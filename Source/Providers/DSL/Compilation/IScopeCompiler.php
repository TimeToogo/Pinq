<?php
namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Interface for a scope compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IScopeCompiler
{
    /**
     * Compiles the supplied scope to the compilation object.
     *
     * @param mixed                              $compilation
     * @param Queries\IScope                     $scope
     * @param Queries\IResolvedParameterRegistry $structuralParameters
     *
     * @return void
     */
    public function compileScope($compilation, Queries\IScope $scope, Queries\IResolvedParameterRegistry $structuralParameters);
}
