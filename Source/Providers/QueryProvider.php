<?php

namespace Pinq\Providers;

use Pinq\Caching;
use Pinq\Expressions as O;
use Pinq\Queries\Builders;
use Pinq\Queries;

/**
 * Base class for the query provider, with default functionality
 * for the function to expression tree converter and request evaluation
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class QueryProvider extends ProviderBase implements IQueryProvider
{
    /**
     * @var Builders\IRequestQueryBuilder
     */
    protected $requestBuilder;

    public function __construct(Queries\ISourceInfo $sourceInfo, Configuration\IQueryConfiguration $configuration = null)
    {
        parent::__construct($sourceInfo, $configuration ? : new Configuration\DefaultQueryConfiguration());

        $this->requestBuilder = $this->configuration->getRequestQueryBuilder();
    }

    public function createQueryable(O\TraversalExpression $scopeExpression = null)
    {
        return new \Pinq\Queryable($this, $this->sourceInfo, $scopeExpression, $this->scheme);
    }

    public function load(O\Expression $requestExpression)
    {
        $resolution = $this->requestBuilder->resolveRequest($requestExpression);
        $queryHash  = $resolution->getHash();
        $query      = $this->queryCache->tryGet($queryHash);

        if (!($query instanceof Queries\IRequestQuery)) {
            $query = $this->requestBuilder->parseRequest($requestExpression);
            $this->queryCache->save($queryHash, $query);
        }

        $resolvedParameters = $query->getParameters()->resolve($resolution);

        return $this->loadRequest($query, $resolvedParameters);
    }

    /**
     * @param Queries\IRequestQuery              $request
     * @param Queries\IResolvedParameterRegistry $resolvedParameters
     *
     * @return mixed
     */
    abstract protected function loadRequest(
            Queries\IRequestQuery $request,
            Queries\IResolvedParameterRegistry $resolvedParameters
    );
}
