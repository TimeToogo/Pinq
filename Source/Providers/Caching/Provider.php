<?php

namespace Pinq\Providers\Caching;

use Pinq\Queries;

/**
 * Query provider wrapper for caching the request evaluators
 * of equivalent scopes.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Provider extends \Pinq\Providers\QueryProvider
{
    /**
     * @var \Pinq\Providers\QueryProvider
     */
    private $innerQueryProvider;
    
    /**
     * @var \SplObjectStorage
     */
    private $scopeRequestEvaluatorMap;

    public function __construct(\Pinq\Providers\QueryProvider $queryProvider)
    {
        parent::__construct();
        $this->innerQueryProvider = $queryProvider;
        $this->scopeRequestEvaluatorMap = new \SplObjectStorage();
    }
    
    public function createQueryable(Queries\IScope $scope = null)
    {
        return $this->innerQueryProvider->createQueryable($scope);
    }

    public function getFunctionToExpressionTreeConverter()
    {
        return $this->innerQueryProvider->getFunctionToExpressionTreeConverter();
    }

    final protected function loadRequestEvaluatorVisitor(Queries\IScope $scope)
    {
        if(!isset($this->scopeRequestEvaluatorMap[$scope])) {
            $this->scopeRequestEvaluatorMap[$scope] = 
                    $this->getCachedRequestEvaluator($scope) ?: $this->innerQueryProvider->loadRequestEvaluatorVisitor($scope);
        }
        
        return $this->scopeRequestEvaluatorMap[$scope];
    }
    
    /**
     * @param Queries\IScope $scope
     * @return Queries\Requests\RequestVisitor|null
     */
    private function getCachedRequestEvaluator(Queries\IScope $scope)
    {
        foreach($this->scopeRequestEvaluatorMap as $otherScope) {
            if($scope == $otherScope) {
                return $this->scopeRequestEvaluatorMap[$otherScope];
            }
        }
    }
}
