<?php
namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Interface for a operation compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IOperationCompiler
{
    /**
     * @param mixed                              $compilation
     * @param IScopeCompiler                     $scopeCompiler
     * @param Queries\IOperation                 $operation
     * @param Queries\IResolvedParameterRegistry $structuralParameters
     *
     * @return mixed
     */
    public function compileOperation(
            $compilation,
            IScopeCompiler $scopeCompiler,
            Queries\IOperation $operation,
            Queries\IResolvedParameterRegistry $structuralParameters
    );
}
