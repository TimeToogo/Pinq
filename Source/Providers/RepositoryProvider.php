<?php

namespace Pinq\Providers;

use Pinq\Expressions as O;
use Pinq\Queries\Builders;
use Pinq\Queries;
use Pinq\Repository;

/**
 * Base class for the repository provider.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
abstract class RepositoryProvider extends ProviderBase implements IRepositoryProvider
{
    /**
     * @var Configuration\IRepositoryConfiguration
     */
    protected $configuration;

    /**
     * @var IQueryProvider
     */
    protected $queryProvider;

    /**
     * @var Builders\IOperationQueryBuilder
     */
    protected $operationQueryBuilder;

    public function __construct(
            Queries\ISourceInfo $sourceInfo,
            IQueryProvider $queryProvider,
            Configuration\IRepositoryConfiguration $configuration = null
    ) {
        parent::__construct($sourceInfo, $configuration ?: new Configuration\DefaultRepositoryConfiguration());

        $this->queryProvider         = $queryProvider;
        $this->queryResultCollection = $queryProvider->getQueryResultCollection();
        $this->operationQueryBuilder = $this->configuration->getOperationQueryBuilder();
    }

    final public function getQueryProvider()
    {
        return $this->queryProvider;
    }

    final public function getQueryResultCollection()
    {
        return $this->queryResultCollection;
    }

    final public function createQueryable(O\TraversalExpression $scopeExpression = null)
    {
        return $this->queryProvider->createQueryable($scopeExpression);
    }

    public function createRepository(O\TraversalExpression $scopeExpression = null)
    {
        return new Repository($this, $this->sourceInfo, $scopeExpression, $this->scheme);
    }

    final public function load(O\Expression $requestExpression)
    {
        return $this->queryProvider->load($requestExpression);
    }

    public function execute(O\Expression $operationExpression)
    {
        $this->executeOperationExpression($operationExpression);
        if ($this->queryResultCollection !== null) {
            $this->queryResultCollection->clearResults();
        }
    }

    protected function executeOperationExpression(O\Expression $operationExpression)
    {
        $resolution = $this->operationQueryBuilder->resolveOperation($operationExpression);
        $queryHash  = $resolution->getHash();
        $query      = $this->queryCache->tryGet($queryHash);

        if (!($query instanceof Queries\IOperationQuery)) {
            $query = $this->operationQueryBuilder->parseOperation($operationExpression);
            $this->queryCache->save($queryHash, $query);
        }

        $resolvedParameters = $query->getParameters()->resolve($resolution);

        $this->executeOperation($query, $resolvedParameters);
    }

    /**
     * @param \Pinq\Queries\IOperationQuery            $operation
     * @param \Pinq\Queries\IResolvedParameterRegistry $resolvedParameters
     *
     * @return void
     */
    abstract protected function executeOperation(
            Queries\IOperationQuery $operation,
            Queries\IResolvedParameterRegistry $resolvedParameters
    );
}
