<?php

namespace Pinq;

use Pinq\Queries;

/**
 * Implements the result API for a join / group join queryable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningToQueryable implements IJoiningToTraversable
{
    /**
     * @var Providers\IQueryProvider
     */
    private $provider;

    /**
     * @var Queries\IScope
     */
    private $scope;

    /**
     * @var callable
     */
    private $constructSegmentFunction;

    public function __construct(Providers\IQueryProvider $provider, Queries\IScope $scope, callable $constructSegmentFunction)
    {
        $this->provider = $provider;
        $this->scope = $scope;
        $this->constructSegmentFunction = $constructSegmentFunction;
    }

    public function to(callable $joinFunction)
    {
        $constructSegmentFunction = $this->constructSegmentFunction;

        return $this->provider->createQueryable($this->scope->append($constructSegmentFunction($joinFunction)));
    }
}
