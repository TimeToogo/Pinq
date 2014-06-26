<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Queries;
use Pinq\Providers;

/**
 * Implements the filtering API for a join / group join repository.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningRepository extends JoiningQueryable implements Interfaces\IJoiningOnRepository
{
    /**
     * @var Providers\IRepositoryProvider
     */
    protected $provider;
    
    public function __construct(Providers\IRepositoryProvider $provider, Queries\IScope $scope, $innerValues, $isGroupJoin)
    {
        parent::__construct($provider, $scope, $innerValues, $isGroupJoin);
    }
    
    public function apply(callable $applyFunction)
    {
        $this->provider->execute(new Queries\OperationQuery(
                $this->scope,
                new Queries\Operations\JoinApply(
                        $this->innerValues, 
                        $this->isGroupJoin,
                        $this->filter,
                        $this->functionConverter->convert($applyFunction))));
    }
}
