<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\IQueryable;
use Pinq\Providers\DSL\IRepositoryCompilerConfiguration;
use Pinq\Providers;
use Pinq\Queries;
use Pinq\Tests\Integration\Providers\DSL\Implementation\DummyDSLQueryProvider;
use Pinq\Tests\Integration\Providers\DSL\Implementation\DummyDSLRepositoryProvider;
use Pinq\Tests\Integration\Queries\QueryBuildingTest;
use Pinq\Tests\Integration\Queries\ParsedQueryBuildingTest;

abstract class DSLCompilationProviderTest extends ParsedQueryBuildingTest
{
    /**
     * @var IRepositoryCompilerConfiguration
     */
    protected $compilerConfig;

    /**
     * @return IRepositoryCompilerConfiguration
     */
    abstract protected function compilerConfiguration();

    public function queryProviders()
    {
        return [new DummyDSLQueryProvider(new Queries\SourceInfo(''), $this->compilerConfiguration())];
    }

    public function repositoryProviders()
    {
        return [new DummyDSLRepositoryProvider(new Queries\SourceInfo(''), $this->compilerConfiguration())];
    }

    protected function assertRequestQueryMatches(
            Queries\IRequestQuery $requestQuery,
            Queries\IResolvedParameterRegistry $resolvedParameters,
            $correctValue
    )
    {
        /** @var $configuration IRepositoryCompilerConfiguration */
        $configuration = $this->queryable->getProvider()->getCompilerConfiguration();
        $requestQueryCompiler = $configuration->getRequestQueryCompiler();
        $template = $requestQueryCompiler->createRequestTemplate($requestQuery, Queries\ResolvedParameterRegistry::none());
        $compiledString = (string) $requestQueryCompiler->compileRequestQuery($template, Queries\ResolvedParameterRegistry::none());

        $this->assertSame($correctValue, $compiledString);
    }

    protected function assertOperationQueryMatches(
            Queries\IOperationQuery $operationQuery,
            Queries\IResolvedParameterRegistry $resolvedParameters,
            $correctValue
    )
    {
        /** @var $configuration IRepositoryCompilerConfiguration */
        $configuration = $this->queryable->getProvider()->getCompilerConfiguration();
        $operationQueryCompiler = $configuration->getOperationQueryCompiler();
        $template = $operationQueryCompiler->createOperationTemplate($operationQuery, Queries\ResolvedParameterRegistry::none());
        $compiledString = (string) $operationQueryCompiler->compileOperationQuery($template, Queries\ResolvedParameterRegistry::none());

        $this->assertSame($correctValue, $compiledString);
    }
}
