<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation;
use Pinq\Providers\DSL\Compilation\Compilers\IRequestQueryCompiler;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Providers\DSL\Compilation\Processors\Compilers\IOperationQueryCompiler;
use Pinq\Queries;
use Pinq\Tests\Integration\Providers\DSL\Implementation\ConfigurationBase;

class Configuration extends ConfigurationBase
{
    protected function makeCompiledRequestQuery(Queries\IRequestQuery $query)
    {
        $compiledQuery = new CompiledQuery();
        $compiler = new RequestCompiler($compiledQuery, $query);
        $compiler->buildQuery();

        return $compiledQuery;
    }

    protected function makeCompiledOperationQuery(Queries\IOperationQuery $query)
    {
        $compiledQuery = new CompiledQuery();
        $compiler = new OperationCompiler($compiledQuery, $query);
        $compiler->buildQuery();

        return $compiledQuery;
    }

    protected function buildRequestQueryCompiler(Queries\IRequestQuery $query)
    {
        return new RequestCompiler($query, new QueryCompilation());
    }

    protected function buildOperationQueryCompiler(Queries\IOperationQuery $query)
    {
        return new OperationCompiler($query, new QueryCompilation());
    }
}
