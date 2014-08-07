<?php

namespace Pinq\Providers\DSL\Compilation;

use Pinq\Queries;

interface IOperationQueryCompiler
{
    /**
     * Creates a compilation template from the supplied operation query.
     *
     * @param Queries\IOperationQuery            $operationQuery
     * @param Queries\IResolvedParameterRegistry $structuralParameters
     *
     * @return IOperationTemplate
     */
    public function createOperationTemplate(
            Queries\IOperationQuery $operationQuery,
            Queries\IResolvedParameterRegistry $structuralParameters
    );

    /**
     * Compiles the supplied operation template using the parameters that affect
     * the compilation output. e.g variable method calls.
     *
     * @param IOperationTemplate                 $template
     * @param Queries\IResolvedParameterRegistry $structuralParameters
     *
     * @return ICompiledOperation
     */
    public function compileOperationQuery(
            IOperationTemplate $template,
            Queries\IResolvedParameterRegistry $structuralParameters
    );
}
