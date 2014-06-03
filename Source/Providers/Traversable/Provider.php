<?php

namespace Pinq\Providers\Traversable;

use Pinq\Queries;

/**
 * Query provider for evaluating query of the supplied traversable instance,
 * this is useful for mocking a queryable against an in memory traversable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Provider extends \Pinq\Providers\QueryProvider
{
    private $scopeEvaluator;

    protected $traversable;
    
    /**
     * @var \SplObjectStorage
     */
    protected $traversableCache;

    public function __construct(\Pinq\ITraversable $traversable)
    {
        parent::__construct();
        $this->scopeEvaluator = new ScopeEvaluator();
        $this->traversableCache = new \SplObjectStorage();
        $this->traversable = $traversable;
    }

    protected function loadRequestEvaluatorVisitor(Queries\IScope $scope)
    {
        if(!isset($this->traversableCache[$scope])) {
            $this->scopeEvaluator->setTraversable($this->traversable);
            $this->scopeEvaluator->walk($scope);
            
            $this->traversableCache[$scope] = $this->scopeEvaluator->getTraversable();
        }

        return new RequestEvaluator($this->traversableCache[$scope]);
    }
}
