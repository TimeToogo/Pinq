<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\DSL\Compilation;
use Pinq\Providers\DSL\Compilation\Processors;
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

    protected function structuralExpressionProcessors(Queries\IQuery $query)
    {
        return $this->structuralExpressionProcessors;
    }

    abstract protected function makeCompiledRequestQuery(Queries\IRequestQuery $query);

    final protected function preprocessQuery(Queries\IQuery $query)
    {
        foreach($this->processorFactories as $processorFactory) {
            /** @var $processor Processors\IQueryProcessor */
            $processor = $processorFactory($query);
            $query = $processor->buildQuery();
        }

        return $query;
    }

    protected function buildCompiledRequestQuery(Queries\IRequestQuery $query)
    {
        return $this->makeCompiledRequestQuery($this->preprocessQuery($query));
    }

    abstract protected function makeCompiledOperationQuery(Queries\IOperationQuery $query);

    protected function buildCompiledOperationQuery(Queries\IOperationQuery $query)
    {
        return $this->makeCompiledOperationQuery($this->preprocessQuery($query));
    }
}
