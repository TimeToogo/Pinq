<?php

namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Providers\DSL\Compilation\Compilers\IRequestQueryCompiler;
use Pinq\Providers\DSL\Compilation\ICompiledQuery;
use Pinq\Providers\DSL\Compilation\IQueryTemplate;
use Pinq\Providers\DSL\Compilation\IStaticQueryTemplate;
use Pinq\Providers\DSL\Compilation\IStaticRequestTemplate;
use Pinq\Providers\DSL\Compilation\Parameters;
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
        return Caching\CacheProvider::getCacheAdapter()->forNamespace(get_class($this));
    }

    public function getCompiledQueryCache(Queries\ISourceInfo $sourceInfo)
    {
        return $this->compiledQueryCache->forNamespace($sourceInfo->getHash());
    }

    public function getCompiledRequestQueryHash(
            O\Expression $requestExpression,
            /* out */ O\IEvaluationContext $evaluationContext = null)
    {
        $queryTemplate = $this->loadRequestQueryTemplate(
                $requestExpression,
                $evaluationContext,
                /* out */ $resolvedParameters,
                /* out */ $queryCache,
                /* out */ $templateHash
        );

        if ($queryTemplate instanceof IStaticRequestTemplate) {
            return $templateHash;
        }

        $queryTemplate->resolveStructuralParameters($resolvedParameters, /* out */ $structuralHash);

        return $templateHash . '-' . $structuralHash;
    }

    public function loadCompiledRequestQuery(
            O\Expression $requestExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null
    ) {
        $requestTemplate = $this->loadRequestQueryTemplate(
                $requestExpression,
                $evaluationContext,
                /* out */ $resolvedParameters,
                /* out */ $queryCache,
                /* out */ $templateHash
        );

        return $this->loadCompiledQueryFromTemplate(
                $queryCache,
                $templateHash,
                $requestTemplate,
                $resolvedParameters,
                [$this, 'compileRequestQuery']
        );
    }

    protected function loadRequestQueryTemplate(
            O\Expression $queryExpression,
            O\IEvaluationContext $evaluationContext = null,
            /* out */ Queries\IResolvedParameterRegistry &$resolvedParameters = null,
            /* out */ Caching\ICacheAdapter &$queryCache = null,
            /* out */ &$templateHash)
    {
        return $this->loadQueryTemplate($queryExpression,
                $evaluationContext,
                $resolvedParameters,
                [$this->requestQueryBuilder, 'resolveRequest'],
                [$this->requestQueryBuilder, 'parseRequest'],
                [$this, 'createRequestTemplate'],
                $queryCache,
                $templateHash);
    }

    protected function loadQueryTemplate(
            O\Expression $queryExpression,
            O\IEvaluationContext $evaluationContext = null,
            Queries\IResolvedParameterRegistry &$resolvedParameters = null,
            callable $resolveQueryCallback,
            callable $parseQueryCallback,
            callable $createTemplateCallback,
            /* out */ Caching\ICacheAdapter &$queryCache = null,
            /* out */ &$templateHash
    ) {
        /** @var $resolution Queries\IResolvedQuery */
        $resolution   = $resolveQueryCallback($queryExpression, $evaluationContext);
        $templateHash = $resolution->getHash();

        $queryCache    = $this->getCompiledQueryCache($resolution->getQueryable()->getSourceInfo());
        $queryTemplate = $queryCache->tryGet($templateHash);

        if (!($queryTemplate instanceof Compilation\IQueryTemplate)) {
            /** @var $query Queries\IQuery */
            $query = $parseQueryCallback($queryExpression, $evaluationContext);
            /** @var $queryTemplate Compilation\IQueryTemplate */
            $queryTemplate = $createTemplateCallback($query);
            $queryCache->save($templateHash, $queryTemplate);
        }

        $resolvedParameters = $queryTemplate->getParameters()->resolve($resolution);

        return $queryTemplate;
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

        $resolvedStructuralParameters = $template->resolveStructuralParameters($parameters, $hash);
        $compiledQueryHash            = md5($templateHash . '-' . $hash);
        $compiledQuery                = $queryCache->tryGet($compiledQueryHash);

        if (!($compiledQuery instanceof ICompiledQuery)) {
            $compiledQuery = $compileRequestCallback($template, $resolvedStructuralParameters);
            $queryCache->save($compiledQueryHash, $compiledQuery);
        }

        return $compiledQuery;
    }

    /**
     * Returns a registry of all the structural parameters of the query.
     *
     * @param Queries\IQuery $query
     *
     * @return Parameters\ParameterRegistry
     */
    abstract protected function locateStructuralParameters(Queries\IQuery $query);

    protected function createRequestTemplate(Queries\IRequestQuery $requestQuery)
    {
        $structuralParameters = $this->locateStructuralParameters($requestQuery);

        if ($structuralParameters->count() === 0) {
            return new StaticRequestTemplate($requestQuery->getParameters(), $this->buildCompiledRequestQuery(
                    $requestQuery
            ));
        }

        return new RequestTemplate($requestQuery, $structuralParameters);
    }

    /**
     * Creates a new query with inlined resolved structural parameters.
     *
     * @param Queries\IQuery                       $query
     * @param Parameters\ResolvedParameterRegistry $structuralParameters
     *
     * @return Queries\IRequestQuery|Queries\IOperationQuery
     */
    abstract protected function inlineStructuralParameters(
            Queries\IQuery $query,
            Parameters\ResolvedParameterRegistry $structuralParameters
    );

    public function compileRequestQuery(
            Compilation\IRequestTemplate $template,
            Parameters\ResolvedParameterRegistry $structuralExpressions
    ) {
        $structuredQuery = $this->inlineStructuralParameters($template->getQuery(), $structuralExpressions);

        return $this->buildCompiledRequestQuery($structuredQuery);
    }

    /**
     * @param Queries\IRequestQuery $query
     *
     * @return Compilation\ICompiledRequest
     */
    protected function buildCompiledRequestQuery(Queries\IRequestQuery $query)
    {
        $compiler = $this->getRequestQueryCompiler($query);
        $compiler->compile();

        return $compiler->getCompilation()->asCompiled();
    }

    /**
     * @param Queries\IRequestQuery $query
     *
     * @return IRequestQueryCompiler
     */
    abstract protected function getRequestQueryCompiler(Queries\IRequestQuery $query);
}
