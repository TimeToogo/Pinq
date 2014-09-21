<?php

namespace Pinq\Providers\Collection;

use Pinq\ICollection;
use Pinq\Providers\Traversable;
use Pinq\Providers;
use Pinq\Queries;

/**
 * Repository provider for evaluating query of the supplied collection instance,
 * this is useful for mocking a repository against an in memory collection.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Provider extends Providers\RepositoryProvider
{
    /**
     * @var Traversable\Provider
     */
    protected $queryProvider;

    /**
     * @var ICollection
     */
    protected $collection;

    public function __construct(ICollection $collection)
    {
        parent::__construct(new Traversable\SourceInfo($collection), new Traversable\Provider($collection));

        $this->scheme     = $collection->getIteratorScheme();
        $this->collection = $collection;
    }

    protected function executeOperation(
            Queries\IOperationQuery $query,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $scopedCollection = $this->queryProvider->evaluateScope($query->getScope(), $resolvedParameters);

        OperationEvaluator::evaluate($scopedCollection, $query->getOperation(), $resolvedParameters);
    }
}
