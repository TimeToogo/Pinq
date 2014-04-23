<?php

namespace Pinq\Providers\Collection;

use Pinq\Queries;

/**
 * Repository provider for evalating query of the supplied collection instance,
 * this is useful for mocking a repository against an in memory collection.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Provider extends \Pinq\Providers\RepositoryProvider
{
    private $collection;

    private $traversableProvider;

    private $scopeEvaluator;

    public function __construct(\Pinq\ICollection $collection)
    {
        parent::__construct();
        $this->collection = $collection;
        $this->scopeEvaluator = new \Pinq\Providers\Traversable\ScopeEvaluator();
        $this->traversableProvider = new \Pinq\Providers\Traversable\Provider($collection);
    }

    protected function loadOperationEvaluatorVisitor(Queries\IScope $scope)
    {
        $this->scopeEvaluator->setTraversable($this->collection);
        $this->scopeEvaluator->walk($scope);

        return new OperationEvaluator($this->scopeEvaluator->getTraversable()->asCollection());
    }

    public function load(Queries\IRequestQuery $query)
    {
        return $this->traversableProvider->load($query);
    }

    protected function loadRequestEvaluatorVisitor(Queries\IScope $scope)
    {

    }
}
