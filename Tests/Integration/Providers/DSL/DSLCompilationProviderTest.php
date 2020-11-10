<?php

namespace Pinq\Tests\Integration\Providers\DSL;

use Pinq\Expressions as O;
use Pinq\Parsing;
use Pinq\Providers;
use Pinq\Providers\DSL\Compilation;
use Pinq\Queries;
use Pinq\Tests\Integration\Providers\DSL\Implementation\DummyDSLQueryProvider;
use Pinq\Tests\Integration\Providers\DSL\Implementation\DummyDSLRepositoryProvider;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;
use Pinq\Tests\Integration\Queries\ParsedQueryBuildingTest;

abstract class DSLCompilationProviderTest extends ParsedQueryBuildingTest
{
    /**
     * @var Implementation\SpyingCache
     */
    protected $compiledQueryCache;

    /**
     * @var Implementation\ConfigurationBase
     */
    protected $compilerConfiguration;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var $provider Implementation\DummyDSLQueryProvider */
        $provider = $this->queryable->getProvider();
        $this->compilerConfiguration = $provider->getCompilerConfiguration();
        $this->compiledQueryCache = $this->compilerConfiguration->getCompiledQueryCache($this->queryable->getSourceInfo());
    }

    /**
     * @return Implementation\ConfigurationBase
     */
    abstract protected function compilerConfiguration();

    /**
     * @return callable[]
     */
    protected function preprocessorFactories()
    {
        return [];
    }

    /**
     * @return IStructuralExpressionProcessor[]
     */
    protected function structuralExpressionProcessors()
    {
        return [];
    }

    private function makeCompilerConfiguration()
    {
        $configuration = $this->compilerConfiguration();
        $configuration->setProcessorFactories($this->preprocessorFactories());
        $configuration->setStructuralExpressionProcessors($this->structuralExpressionProcessors());

        return $configuration;
    }

    public function queryProviders()
    {
        return [new DummyDSLQueryProvider(new Queries\SourceInfo(''), $this->makeCompilerConfiguration())];
    }

    public function repositoryProviders()
    {
        return [new DummyDSLRepositoryProvider(new Queries\SourceInfo(''), $this->makeCompilerConfiguration())];
    }

    protected function assertRequestExpressionMatches(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            $correctValue
    ) {
        $compiledQuery = $this->loadCompiledRequestQuery($requestExpression, $evaluationContext);
        $this->assertQueryCompiledCorrectly($compiledQuery, $correctValue);
    }

    protected function loadCompiledRequestQuery(O\Expression $requestExpression, O\IEvaluationContext $evaluationContext = null)
    {
        /** @var $configuration Implementation\ConfigurationBase */
        $configuration = $this->queryable->getProvider()->getCompilerConfiguration();
        return $configuration->loadCompiledRequestQuery($requestExpression, $evaluationContext, $resolvedParameters);
    }

    protected function assertOperationExpressionMatches(
            O\Expression $operationExpression,
            O\IEvaluationContext $evaluationContext = null,
            $correctValue
    ) {
        $compiledQuery = $this->loadCompiledOperationQuery($operationExpression, $evaluationContext);

        $this->assertQueryCompiledCorrectly($compiledQuery, $correctValue);
    }

    protected function loadCompiledOperationQuery(O\Expression $operationExpression, O\IEvaluationContext $evaluationContext = null)
    {
        /** @var $configuration Implementation\ConfigurationBase */
        $configuration = $this->queryable->getProvider()->getCompilerConfiguration();
        return $configuration->loadCompiledOperationQuery($operationExpression, $evaluationContext, $resolvedParameters);
    }

    protected function assertQueryCompiledCorrectly($compiledQuery, $correctValue)
    {
        $this->assertSame($correctValue, (string)$compiledQuery);
    }

    protected function assertRequestQueryMatches(Queries\IRequestQuery $requestQuery, Queries\IResolvedParameterRegistry $resolvedParameters, $correctValue)
    {

    }

    protected function assertOperationQueryMatches(Queries\IOperationQuery $operationQuery, Queries\IResolvedParameterRegistry $resolvedParameters, $correctValue)
    {

    }
}
