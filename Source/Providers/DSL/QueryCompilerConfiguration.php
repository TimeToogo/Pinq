<?php

namespace Pinq\Providers\DSL;

use Pinq\Caching;
use Pinq\Iterators;
use Pinq\Parsing;
use Pinq\Queries;
use Pinq\Queries\Builders;

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
     * @var Compilation\IRequestQueryCompiler
     */
    protected $requestQueryCompiler;

    /**
     * @var Compilation\IRequestCompiler
     */
    protected $requestCompiler;

    /**
     * @var Compilation\IScopeCompiler
     */
    protected $scopeCompiler;

    public function __construct()
    {
        $this->compiledQueryCache   = $this->buildCompiledQueryCache();
        $this->scopeCompiler        = $this->buildScopeCompiler();
        $this->requestCompiler      = $this->buildRequestCompiler();
        $this->requestQueryCompiler = $this->buildRequestQueryCompiler();
    }

    protected function buildCompiledQueryCache()
    {
        return Caching\Provider::getCacheAdapter()->forNamespace(get_class($this));
    }

    /**
     * @return Compilation\IScopeCompiler
     */
    abstract protected function buildScopeCompiler();

    /**
     * @return Compilation\IRequestCompiler
     */
    abstract protected function buildRequestCompiler();

    /**
     * @return Compilation\IRequestQueryCompiler
     */
    abstract protected function buildRequestQueryCompiler();

    public function getCompiledQueryCache(Queries\ISourceInfo $sourceInfo)
    {
        return $this->compiledQueryCache->forNamespace($sourceInfo->getHash());
    }

    public function getRequestQueryCompiler()
    {
        return $this->requestQueryCompiler;
    }

    public function getScopeCompiler()
    {
        return $this->scopeCompiler;
    }

    public function getRequestCompiler()
    {
        return $this->requestCompiler;
    }
}
