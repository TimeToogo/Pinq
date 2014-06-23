<?php

namespace Pinq\Providers\Traversable;

use Pinq\Queries;
use Pinq\Iterators\IIteratorScheme;

/**
 * Query provider for evaluating query of the supplied traversable instance,
 * this is useful for mocking a queryable against an in memory traversable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Provider extends \Pinq\Providers\QueryProvider
{
    /**
     * @var ScopeEvaluator
     */
    protected $scopeEvaluator;
    
    /**
     * @var \Pinq\ITraversable
     */
    protected $traversable;
    
    /**
     * @var \SplObjectStorage
     */
    protected $traversableCache;

    public function __construct(\Pinq\ITraversable $traversable)
    {
        parent::__construct(null, null, $traversable->getIteratorScheme());
        
        $this->scopeEvaluator = new ScopeEvaluator();
        $this->traversableCache = new \SplObjectStorage();
        $this->traversable = $traversable;
    }
    
    public function evaluateScope(Queries\IScope $scope)
    {
        if(!isset($this->traversableCache[$scope])) {
            $this->scopeEvaluator->setTraversable($this->traversable);
            $this->scopeEvaluator->walk($scope);
            
            $this->traversableCache[$scope] = $this->scopeEvaluator->getTraversable();
        }
        
        return $this->traversableCache[$scope];
    }

    protected function loadRequestEvaluatorVisitor(Queries\IScope $scope)
    {
        return new RequestEvaluator($this->evaluateScope($scope));
    }
}
