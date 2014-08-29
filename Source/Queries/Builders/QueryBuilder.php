<?php

namespace Pinq\Queries\Builders;

use Pinq\Queries;

/**
 * Base class of the request / operation query builder.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryBuilder extends BaseQueryBuilder
{
    /**
     * @var IScopeBuilder
     */
    protected $scopeBuilder;

    public function __construct(IScopeBuilder $scopeBuilder)
    {
        parent::__construct($scopeBuilder->getFunctionInterpreter());
        $this->scopeBuilder = $scopeBuilder;
    }

    protected function buildResolvedQuery(Interpretations\IScopeResolver $scopeResolver, Interpretations\IQueryResolver $queryResolver)
    {
        return new Queries\ResolvedQuery(
                $scopeResolver->getQueryable(),
                $scopeResolver->getResolvedParameters() + $queryResolver->getResolvedParameters(),
                md5($scopeResolver->getHash() . '#' . $queryResolver->getHash()));
    }
}
