<?php

namespace Pinq\Providers\Configuration;

use Pinq\Caching;
use Pinq\Iterators;
use Pinq\Parsing;
use Pinq\Queries\Builders;

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

    public function __construct()
    {
        $this->iteratorScheme      = $this->buildIteratorScheme();
        $this->queryCache          = $this->buildQueryCache();
        $this->functionInterpreter = $this->buildFunctionInterpreter();
        $this->scopeBuilder        = $this->buildScopeQueryBuilder();
        $this->requestQueryBuilder = $this->buildRequestQueryBuilder();
    }

    protected function buildIteratorScheme()
    {
        return Iterators\SchemeProvider::getDefault();
    }

    protected function buildQueryCache()
    {
        return Caching\Provider::getCache();
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

    final public function getIteratorScheme()
    {
        return $this->iteratorScheme;
    }

    final public function getQueryCache()
    {
        return $this->queryCache;
    }

    final public function getRequestQueryBuilder()
    {
        return $this->requestQueryBuilder;
    }
}
