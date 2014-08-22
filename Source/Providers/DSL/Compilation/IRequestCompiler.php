<?php
namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Interface for a request compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestCompiler
{
    /**
     * @param mixed                              $compilation
     * @param IScopeCompiler                     $scopeCompiler
     * @param Queries\IRequest                   $request
     * @param Queries\IResolvedParameterRegistry $structuralParameters
     *
     * @return void
     */
    public function compileRequest(
            $compilation,
            IScopeCompiler $scopeCompiler,
            Queries\IRequest $request,
            Queries\IResolvedParameterRegistry $structuralParameters
    );
}
