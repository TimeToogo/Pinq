<?php

namespace Pinq\Providers\Configuration;

use Pinq\Caching;
use Pinq\Iterators;
use Pinq\Parsing;
use Pinq\Providers\Utilities;
use Pinq\Queries\Builders;
use Pinq\Traversable;

/**
 * Implementation of the query configuration using standard
 * classes from the Pinq library.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class DefaultQueryConfiguration implements IQueryConfiguration
{
    /**
     * @var Caching\IQueryCache
     */
    protected $queryCache;

    /**
     * @var Iterators\IIteratorScheme
     */
    protected $iteratorScheme;

    /**
     * @var Parsing\IFunctionInterpreter
     */
    protected $functionInterpreter;

    /**
     * @var Builders\IScopeBuilder
     */
    protected $scopeBuilder;

    /**
     * @var Builders\IRequestQueryBuilder
     */
    protected $requestQueryBuilder;

    /**
     * @var boolean
     */
    protected $shouldUseQueryResultCaching;

    public function __construct()
    {
        $this->iteratorScheme              = $this->buildIteratorScheme();
        $this->queryCache                  = $this->buildQueryCache();
        $this->functionInterpreter         = $this->buildFunctionInterpreter();
        $this->scopeBuilder                = $this->buildScopeQueryBuilder();
        $this->requestQueryBuilder         = $this->buildRequestQueryBuilder();
        $this->shouldUseQueryResultCaching = $this->shouldUseQueryResultCaching();
    }

    protected function buildIteratorScheme()
    {
        return Iterators\SchemeProvider::getDefault();
    }

    protected function buildQueryCache()
    {
        return Caching\CacheProvider::getCache();
    }

    protected function buildFunctionInterpreter()
    {
        return Parsing\FunctionInterpreter::getDefault();
    }

    protected function buildRequestQueryBuilder()
    {
        return new Builders\RequestQueryBuilder($this->scopeBuilder);
    }

    protected function buildScopeQueryBuilder()
    {
        return new Builders\ScopeBuilder($this->functionInterpreter);
    }

    protected function shouldUseQueryResultCaching()
    {
        return false;
    }

    protected function buildQueryResultCollection()
    {
        return new Utilities\QueryResultCollection(Traversable::factory($this->buildIteratorScheme()));
    }

    final public function getQueryResultCollection()
    {
        return $this->shouldUseQueryResultCaching ? $this->buildQueryResultCollection() : null;
    }

    public function getIteratorScheme()
    {
        return $this->iteratorScheme;
    }

    public function getQueryCache()
    {
        return $this->queryCache;
    }

    public function getRequestQueryBuilder()
    {
        return $this->requestQueryBuilder;
    }
}
