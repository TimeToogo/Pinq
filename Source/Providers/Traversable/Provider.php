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

    public function __construct(\Pinq\ITraversable $traversable)
    {
        parent::__construct();
        $this->scopeEvaluator = new ScopeEvaluator();
        $this->traversable = $traversable;
    }

    protected function loadRequestEvaluatorVisitor(Queries\IScope $scope)
    {
        $this->scopeEvaluator->setTraversable($this->traversable);
        $this->scopeEvaluator->walk($scope);

        return new RequestEvaluator($this->scopeEvaluator->getTraversable());
    }
}
