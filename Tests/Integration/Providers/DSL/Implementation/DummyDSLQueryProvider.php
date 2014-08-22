<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation;

use Pinq\Providers\DSL\Compilation;
use Pinq\Providers\DSL\QueryProvider;
use Pinq\Queries;

class DummyDSLQueryProvider extends QueryProvider
{
    protected function loadCompiledRequest(
            Compilation\ICompiledRequest $compiledRequest,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        return null;
    }
}
