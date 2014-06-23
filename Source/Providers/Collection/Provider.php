<?php

namespace Pinq\Providers\Collection;

use Pinq\Queries;
use \Pinq\Providers;

/**
 * Repository provider for evalating query of the supplied collection instance,
 * this is useful for mocking a repository against an in memory collection.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Provider extends \Pinq\Providers\RepositoryProvider
{
    /**
     * @var \Pinq\ICollection
     */
    protected $collection;

    /**
     * @var Providers\Traversable\Provider
     */
    protected $traversableProvider;
    
    /**
     * @var Providers\Traversable\ScopeEvaluator
     */
    protected $scopeEvaluator;

    public function __construct(\Pinq\ICollection $collection)
    {
        parent::__construct(null, null, $collection->getIteratorScheme());
        
        $this->collection = $collection;
        $this->traversableProvider = new Providers\Traversable\Provider($collection);
        $this->scopeEvaluator = new Providers\Traversable\ScopeEvaluator();
    }

    protected function loadOperationEvaluatorVisitor(Queries\IScope $scope)
    {
        return new OperationEvaluator($this->traversableProvider->evaluateScope($scope));
    }

    public function load(Queries\IRequestQuery $query)
    {
        return $this->traversableProvider->load($query);
    }

    protected function loadRequestEvaluatorVisitor(Queries\IScope $scope)
    {

    }
}
