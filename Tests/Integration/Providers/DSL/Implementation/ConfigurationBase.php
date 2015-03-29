<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation;
use Pinq\Providers\DSL\Compilation\Compilers\IOperationQueryCompiler;
use Pinq\Providers\DSL\Compilation\Compilers\IRequestQueryCompiler;
use Pinq\Providers\DSL\Compilation\Processors;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Providers\DSL\RepositoryCompilerConfiguration;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;
use Pinq\Queries;

abstract class ConfigurationBase extends RepositoryCompilerConfiguration
{
    /**
     * @var callable[]
     */
    protected $processorFactories = [];

    /**
     * @var IStructuralExpressionProcessor[]
     */
    protected $structuralExpressionProcessors = [];

    /**
     * @param callable[] $processorFactories
     */
    public function setProcessorFactories(array $processorFactories)
    {
        $this->processorFactories = $processorFactories;
    }

    protected function buildCompiledQueryCache()
    {
        return new SpyingCache();
    }

    /**
     * @param Queries\ISourceInfo $sourceInfo
     *
     * @return SpyingCache
     */
    public function getCompiledQueryCache(Queries\ISourceInfo $sourceInfo)
    {
        return $this->compiledQueryCache;
    }

    /**
     * @param IStructuralExpressionProcessor[] $structuralExpressionProcessors
     */
    public function setStructuralExpressionProcessors(array $structuralExpressionProcessors)
    {
        $this->structuralExpressionProcessors = $structuralExpressionProcessors;
    }

    protected function locateStructuralParameters(Queries\IQuery $query)
    {
        $parameters = new Parameters\ParameterCollection();
        foreach($this->structuralExpressionProcessors as $processor) {
            Processors\Structure\StructuralExpressionLocator::processQuery($parameters, $query, $processor);
        }

        return $parameters->buildRegistry();
    }

    protected function inlineStructuralParameters(
            Queries\IQuery $query,
            Parameters\ResolvedParameterRegistry $parameters
    ) {
        foreach($this->structuralExpressionProcessors as $processor) {
            $query = Processors\Structure\StructuralExpressionInliner::processQuery($parameters, $query, $processor);
        }

        return $query;
    }

    abstract protected function makeCompiledRequestQuery(Queries\IRequestQuery $query);

    /**
     * @param Queries\IQuery $query
     *
     * @return Queries\IRequestQuery|Queries\IOperationQuery
     */
    final protected function preprocessQuery(Queries\IQuery $query)
    {
        foreach($this->processorFactories as $processorFactory) {
            /** @var $processor Processors\IQueryProcessor */
            $processor = $processorFactory($query);
            $query = $processor->buildQuery();
        }

        return $query;
    }

    protected function getRequestQueryCompiler(Queries\IRequestQuery $query)
    {
        return $this->buildRequestQueryCompiler($this->preprocessQuery($query));
    }

    /**
     * @param Queries\IRequestQuery $query
     *
     * @return IRequestQueryCompiler
     */
    abstract protected function buildRequestQueryCompiler(Queries\IRequestQuery $query);

    protected function getOperationQueryCompiler(Queries\IOperationQuery $query)
    {
        return $this->buildOperationQueryCompiler($this->preprocessQuery($query));
    }

    /**
     * @param Queries\IOperationQuery $query
     *
     * @return IOperationQueryCompiler
     */
    abstract protected function buildOperationQueryCompiler(Queries\IOperationQuery $query);
}
