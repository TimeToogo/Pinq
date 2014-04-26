<?php

namespace Pinq\Providers\Loadable;

use Pinq\Queries;

/**
 * Query provider which will cache the requested value scopes
 * and if a loaded parent scope is available, the subscope will be
 * evaluated in memory using the Traversable\Provider.
 * 
 * <code>
 * $someRows = $queryable->where(function ($row) { return $row['id'] <= 50; });
 * 
 * foreach($someRows as $row) {
 *     //This will load the rows
 * }
 *
 * //This will be evaluated in memory
 * $maxId = $someRows->where(function ($row) { return $row['isActive'] === true; });
 * </code>
 * 
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class Provider extends \Pinq\Providers\QueryProvider
{
    /**
     * @var \SplObjectStorage
     */
    private $scopeRequestEvaluatorMap;

    public function __construct(\Pinq\Caching\IFunctionCache $functionCache = null)
    {
        parent::__construct($functionCache);
        
        $this->scopeRequestEvaluatorMap = new \SplObjectStorage();
    }

    final protected function loadRequestEvaluatorVisitor(Queries\IScope $scopeToLoad)
    {
        if(!isset($this->scopeRequestEvaluatorMap[$scopeToLoad])) {
            foreach($this->scopeRequestEvaluatorMap as $scope) {
                if($scopeToLoad->isSubscopeOf($scope)) {
                    $parentScopeRequestEvaluator = $this->scopeRequestEvaluatorMap[$scope];
                    
                    if($parentScopeRequestEvaluator->isLoaded()) {
                        $subscope = $scopeToLoad->getSubscopeOf($scope);
                        
                        $this->scopeRequestEvaluatorMap[$scopeToLoad] = 
                                $this->evaluateSubScope($parentScopeRequestEvaluator, $subscope);
                        break;
                    }
                }
            }
            if(!isset($this->scopeRequestEvaluatorMap[$scopeToLoad])) {
                $this->scopeRequestEvaluatorMap[$scopeToLoad] = $this->loadRequestEvaluator($scopeToLoad);
            }
        }
        
        return $this->scopeRequestEvaluatorMap[$scopeToLoad];
    }
    
    /**
     * @param Queries\IScope $scope
     * @return RequestEvaluator
     */
    abstract protected function loadRequestEvaluator(Queries\IScope $scope);
    
    /**
     * @param RequestEvaluator $parentScopeEvaluator
     * @param Queries\IScope $subscope
     * @return \Pinq\Providers\Traversable\RequestEvaluator
     */
    private function evaluateSubScope(RequestEvaluator $parentScopeEvaluator, Queries\IScope $subscope)
    {
        $parentScopeTraversable = $parentScopeEvaluator->getLoadedRequestEvaluator()->getTraversable();
        
        $subScopeEvaluator = new \Pinq\Providers\Traversable\ScopeEvaluator($parentScopeTraversable);
        $subScopeEvaluator->walk($subscope);
        
        return new \Pinq\Providers\Traversable\RequestEvaluator($subScopeEvaluator->getTraversable());
    }
}
