<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation\IRequestTemplate;
use Pinq\Providers\DSL\Compilation;
use Pinq\Queries;

class RequestQueryCompiler extends Compilation\RequestQueryCompiler
{
    public function createRequestTemplate(
            Queries\IRequestQuery $requestQuery,
            Queries\IResolvedParameterRegistry $structuralParameters
    ) {
        $compiledQuery = new CompiledQuery();
        $this->compileQuery($compiledQuery, $requestQuery, $structuralParameters);

        return new Compilation\StaticRequestTemplate($requestQuery->getParameters(), $compiledQuery);
    }

    public function compileRequestQuery(
            IRequestTemplate $template,
            Queries\IResolvedParameterRegistry $structuralParameters
    ) {
        /** @var $template Compilation\StaticRequestTemplate */

        return $template->getCompiledQuery();
    }
}
