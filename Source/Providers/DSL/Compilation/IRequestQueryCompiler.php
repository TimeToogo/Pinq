<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

/**
 * Interface of a request query compiler.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IRequestQueryCompiler extends IQueryCompiler
{
    /**
     * Creates a compilation template from the supplied request query.
     *
     * @param Queries\IRequestQuery              $requestQuery
     * @param Queries\IResolvedParameterRegistry $structuralParameters
     *
     * @return IRequestTemplate
     */
    public function createRequestTemplate(
            Queries\IRequestQuery $requestQuery,
            Queries\IResolvedParameterRegistry $structuralParameters
    );

    /**
     * Compiles the supplied request template using the parameters that affect
     * the compilation output. e.g parameter variable method calls.
     *
     * @param IRequestTemplate                   $template
     * @param Queries\IResolvedParameterRegistry $structuralParameters
     *
     * @return ICompiledRequest
     */
    public function compileRequestQuery(
            IRequestTemplate $template,
            Queries\IResolvedParameterRegistry $structuralParameters
    );
}
