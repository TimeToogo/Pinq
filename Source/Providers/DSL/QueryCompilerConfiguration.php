<?php

namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Providers\DSL\Compilation\ICompiledQuery;
use Pinq\Providers\DSL\Compilation\IQueryTemplate;
use Pinq\Providers\DSL\Compilation\IStaticQueryTemplate;
use Pinq\Providers\DSL\Compilation\Parameters;
use Pinq\Providers\DSL\Compilation\Processors\Structure\IStructuralExpressionProcessor;
use Pinq\Providers\DSL\Compilation\Processors\Structure\StructuralExpressionInliner;
use Pinq\Providers\DSL\Compilation\Processors\Structure\StructuralExpressionLocator;
use Pinq\Providers\DSL\Compilation\RequestTemplate;
use Pinq\Providers\DSL\Compilation\StaticRequestTemplate;
use Pinq\Queries;

/**
 * Base class of the query compiler configuration.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryCompilerConfiguration implements IQueryCompilerConfiguration
{
    /**
     * @var Caching\ICacheAdapter
     */
    protected $compiledQueryCache;

    /**
     * @var Configuration\IQueryConfiguration
     */
    protected $queryConfiguration;

    /**
     * @var Queries\Builders\IRequestQueryBuilder
     */
    protected $requestQueryBuilder;

    public function __construct()
    {
        $this->compiledQueryCache  = $this->buildCompiledQueryCache();
        $this->queryConfiguration  = $this->buildQueryConfiguration();
        $this->requestQueryBuilder = $this->queryConfiguration->getRequestQueryBuilder();
    }

    final public function getQueryConfiguration()
    {
        return $this->queryConfiguration;
    }

    protected function buildQueryConfiguration()
    {
        return new Configuration\DefaultQueryConfiguration();
    }

    protected function buildCompiledQueryCache()
    {
        return Caching\Provider::getCacheAdapter()->forNamespace(get_class($this));
    }

    public function getCompiledQueryCache(Queries\ISourceInfo $sourceInfo)
    {
        return $this->compiledQueryCache->forNamespace($sourceInfo->getHash());
    }

    public function loadCompiledRequestQuery(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null
    ) {
        return $this->loadCompiledQuery(
                $requestExpression,
                $evaluationContext,
                $resolvedParameters,
                [$this->requestQueryBuilder, 'resolveRequest'],
                [$this->requestQueryBuilder, 'parseRequest'],
                [$this, 'createRequestTemplate'],
                [$this, 'compileRequestQuery']
        );
    }

    protected function loadCompiledQuery(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null,
            callable $resolveQueryCallback,
            callable $parseQueryCallback,
            callable $createTemplateCallback,
            callable $compileQueryCallback
    ) {
        /** @var $resolution Queries\IResolvedQuery */
        $resolution    = $resolveQueryCallback($requestExpression, $evaluationContext);
        $templateHash  = $resolution->getHash();

        $queryCache    = $this->getCompiledQueryCache($resolution->getQueryable()->getSourceInfo());
        $queryTemplate = $queryCache->tryGet($templateHash);

        if (!($queryTemplate instanceof Compilation\IQueryTemplate)) {
            /** @var $query Queries\IQuery */
            $query              = $parseQueryCallback($requestExpression, $evaluationContext);
            $resolvedParameters = $query->getParameters()->resolve($resolution);
            /** @var $queryTemplate Compilation\IQueryTemplate */
            $queryTemplate      = $createTemplateCallback($query);
            $queryCache->save($templateHash, $queryTemplate);
        } else {
            $resolvedParameters = $queryTemplate->getParameters()->resolve($resolution);
        }

        return $this->loadCompiledQueryFromTemplate(
                $queryCache,
                $templateHash,
                $queryTemplate,
                $resolvedParameters,
                $compileQueryCallback
        );
    }

    protected function loadCompiledQueryFromTemplate(
            Caching\ICacheAdapter $queryCache,
            $templateHash,
            IQueryTemplate $template,
            Queries\IResolvedParameterRegistry $parameters,
            callable $compileRequestCallback
    ) {
        if ($template instanceof IStaticQueryTemplate) {
            return $template->getCompiledQuery();
        }

        $resolvedStructuralExpressions = $template->resolveStructuralExpressions($parameters, $hash);
        $compiledQueryHash             = $templateHash . '-' . $hash;
        $compiledQuery                 = $queryCache->tryGet($compiledQueryHash);

        if (!($compiledQuery instanceof ICompiledQuery)) {
            $compiledQuery = $compileRequestCallback($template, $resolvedStructuralExpressions);
            $queryCache->save($compiledQueryHash, $compiledQuery);
        }

        return $compiledQuery;
    }

    /**
     * @param Queries\IQuery $query
     *
     * @return IStructuralExpressionProcessor[]
     */
    abstract protected function structuralExpressionProcessors(Queries\IQuery $query);

    protected function locateStructuralExpressions(Queries\IQuery $query, &$structuralExpressionProcessors)
    {
        $structuralExpressionProcessors = $this->structuralExpressionProcessors($query);
        $structuralExpressions          = new Parameters\StructuralExpressionCollection();
        foreach ($structuralExpressionProcessors as $processor) {
            $structuralExpressions->add($processor, StructuralExpressionLocator::processQuery($query, $processor));
        }

        return $structuralExpressions->buildRegistry();
    }

    protected function createRequestTemplate(Queries\IRequestQuery $requestQuery)
    {
        $structuralExpressions = $this->locateStructuralExpressions($requestQuery, $structuralExpressionProcessors);

        if ($structuralExpressions->countExpressions() === 0) {
            return new StaticRequestTemplate($requestQuery->getParameters(), $this->buildCompiledRequestQuery(
                    $requestQuery
            ));
        }

        return new RequestTemplate($requestQuery, $structuralExpressions);
    }

    protected function inlineStructuralExpressions(
            IQueryTemplate $queryTemplate,
            Parameters\ResolvedStructuralExpressionRegistry $structuralExpressions
    ) {
        $query = $queryTemplate->getQuery();
        foreach ($structuralExpressions->getProcessors() as $processor) {
            $query = StructuralExpressionInliner::processQuery(
                    $query,
                    $processor,
                    $structuralExpressions->getExpressions($processor)
            );
        }

        return $query;
    }

    public function compileRequestQuery(
            Compilation\IRequestTemplate $template,
            Parameters\ResolvedStructuralExpressionRegistry $structuralExpressions
    ) {
        $structuredQuery = $this->inlineStructuralExpressions($template, $structuralExpressions);

        return $this->buildCompiledRequestQuery($structuredQuery);
    }

    /**
     * @param Queries\IRequestQuery $query
     *
     * @return Compilation\ICompiledRequest
     */
    abstract protected function buildCompiledRequestQuery(Queries\IRequestQuery $query);
}
