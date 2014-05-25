<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Queries;
use Pinq\Providers;

/**
 * Implements the filtering API for a join / group join queryable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningOnRepository extends JoiningOnQueryable implements Interfaces\IJoiningOnRepository
{
    /**
     * @var Providers\IRepositoryProvider
     */
    protected $provider;
    
    public function __construct(Providers\IRepositoryProvider $provider, Queries\IScope $scope, $innerValues, $isGroupJoin)
    {
        parent::__construct($provider, $scope, $innerValues, $isGroupJoin);
    }
    
    protected function createQueryable(Queries\IScope $scope)
    {
        return $this->provider->createRepository($scope);
    }
}
