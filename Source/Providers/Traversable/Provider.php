<?php

namespace Pinq\Providers\Traversable;

use Pinq\Queries;

/**
 * Query provider for evaluating query of the supplied traversable instance,
 * this is useful for mocking a queryable against an in memory traversable.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class Provider extends \Pinq\Providers\QueryProvider
{
    /**
     * @var array<string, \Pinq\ITraversable>
     */
    protected $traversableCache = [];

    public function __construct(\Pinq\ITraversable $traversable)
    {
        parent::__construct(new SourceInfo($traversable));

        $this->scheme           = $traversable->getIteratorScheme();
        $this->traversable      = $traversable;
    }

    public function evaluateScope(
            Queries\IScope $scope,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $scopeHash = $this->scopeHash($scope, $resolvedParameters);
        if (!isset($this->traversableCache[$scopeHash])) {
            $this->traversableCache[$scopeHash] = ScopeEvaluator::evaluate($scope, $resolvedParameters);
        }

        return $this->traversableCache[$scopeHash];
    }

    private function scopeHash(
            Queries\IScope $scope,
            Queries\IResolvedParameterRegistry $resolvedParameters)
    {
        return spl_object_hash($scope) . '-' . spl_object_hash($resolvedParameters);
    }

    protected function loadRequest(
            Queries\IRequestQuery $query,
            Queries\IResolvedParameterRegistry $resolvedParameters
    ) {
        $scopedTraversable = $this->evaluateScope($query->getScope(), $resolvedParameters);

        return RequestEvaluator::evaluate($scopedTraversable, $query->getRequest(), $resolvedParameters);
    }
}
