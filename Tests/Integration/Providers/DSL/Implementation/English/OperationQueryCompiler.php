<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation\IOperationTemplate;
use Pinq\Providers\DSL\Compilation;
use Pinq\Queries;

class OperationQueryCompiler extends Compilation\OperationQueryCompiler
{
    public function createOperationTemplate(
            Queries\IOperationQuery $operationQuery,
            Queries\IResolvedParameterRegistry $structuralParameters
    ) {
        $compiledQuery = new CompiledQuery();
        $this->compileQuery($compiledQuery, $operationQuery, $structuralParameters);

        return new Compilation\StaticOperationTemplate($operationQuery->getParameters(), $compiledQuery);
    }

    public function compileOperationQuery(
            IOperationTemplate $template,
            Queries\IResolvedParameterRegistry $structuralParameters
    ) {
        /** @var $template Compilation\StaticOperationTemplate */

        return $template->getCompiledQuery();
    }
}
