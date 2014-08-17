<?php

namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Providers\Configuration;
use Pinq\Providers\DSL\Compilation\ICompiledRequest;
use Pinq\Providers\DSL\Compilation\IRequestTemplate;
use Pinq\Providers\DSL\Compilation\IStaticQueryTemplate;
use Pinq\Providers;
use Pinq\Queries;

/**
 * Base class for query providers that compile query expressions into
 * another DSL.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryProvider extends Providers\QueryProvider
{
    /**
     * @var IQueryCompilerConfiguration
     */
    protected $compilerConfiguration;

    /**
     * @var Compilation\IRequestQueryCompiler
     */
    protected $requestQueryCompiler;

    /**
     * @var Caching\ICacheAdapter
     */
    protected $compiledQueryCache;

    public function __construct(
            Queries\ISourceInfo $sourceInfo,
            IQueryCompilerConfiguration $compilerConfiguration,
            Configuration\IQueryConfiguration $configuration = null
    ) {
        parent::__construct($sourceInfo, $configuration);

        $this->compilerConfiguration = $compilerConfiguration;
        $this->compiledQueryCache    = $compilerConfiguration->getCompiledQueryCache($sourceInfo);
        $this->requestQueryCompiler  = $compilerConfiguration->getRequestQueryCompiler();
    }

    /**
     * @return IQueryCompilerConfiguration
     */
    public function getCompilerConfiguration()
    {
        return $this->compilerConfiguration;
    }

    public function loadRequestExpression(O\Expression $requestExpression)
    {
        $resolution    = $this->requestBuilder->resolveRequest($requestExpression);
        $queryHash     = $resolution->getHash();
        $queryTemplate = $this->compiledQueryCache->tryGet($queryHash);

        if (!($queryTemplate instanceof Compilation\IRequestTemplate)) {
            $requestQuery       = $this->requestBuilder->parseRequest($requestExpression);
            $resolvedParameters = $requestQuery->getParameters()->resolve($resolution);
            $queryTemplate      = $this->requestQueryCompiler->createRequestTemplate(
                    $requestQuery,
                    $resolvedParameters
            );
            $this->compiledQueryCache->save($queryHash, $queryTemplate);
        } else {
            $resolvedParameters = $queryTemplate->getParameters()->resolve($resolution);
        }

        $compiledQuery = $this->getCompiledQuery($queryHash, $queryTemplate, $resolvedParameters);

        return $this->loadCompiledRequest($compiledQuery, $resolvedParameters);
    }

    final protected function getCompiledQuery(
            $queryHash,
            IRequestTemplate $template,
            Queries\IResolvedParameterRegistry $parameters
    ) {
        if ($template instanceof IStaticQueryTemplate) {
            return $template->getCompiledQuery();
        } else {
            $compiledQueryHash = $queryHash . '-' . $template->getCompiledQueryHash($parameters);
            $compiledQuery     = $this->compiledQueryCache->tryGet($compiledQueryHash);

            if (!($compiledQuery instanceof ICompiledRequest)) {
                $compiledQuery = $this->requestQueryCompiler->compileRequestQuery($template, $parameters);
                $this->compiledQueryCache->save($compiledQueryHash, $compiledQuery);
            }

            return $compiledQuery;
        }
    }

    abstract protected function loadCompiledRequest(
            Compilation\ICompiledRequest $compiledRequest,
            Queries\IResolvedParameterRegistry $parameters
    );

    final protected function loadRequest(
            Queries\IRequestQuery $request,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        //Due to overriding parent::loadRequestExpression, this should never be called.
        throw \Pinq\PinqException::notSupported(__METHOD__);
    }
}
