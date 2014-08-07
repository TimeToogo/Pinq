<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation;
use Pinq\Providers\DSL\RepositoryCompilerConfiguration;
use Pinq\Queries;
use Pinq\Queries\Requests;
use Pinq\Queries\Segments;

class Configuration extends RepositoryCompilerConfiguration
{
    protected function buildScopeCompiler()
    {
        return new ScopeCompiler();
    }

    protected function buildRequestCompiler()
    {
        return new RequestCompiler();
    }

    protected function buildRequestQueryCompiler()
    {
        return new RequestQueryCompiler($this->requestCompiler, $this->scopeCompiler);
    }

    protected function buildOperationCompiler()
    {
        return new OperationCompiler();
    }

    protected function buildOperationQueryCompiler()
    {
        return new OperationQueryCompiler($this->operationCompiler, $this->scopeCompiler);
    }
}
