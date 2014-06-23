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
        $joinFactory = $this->joinSegmentFactory;
        $join = $joinFactory(function () {});
        
        if($join instanceof Queries\Segments\EqualityJoin) {
            $this->provider->execute(new Queries\OperationQuery(
                    $this->scope, 
                    new Queries\Operations\EqualityJoinApply(
                            $join->getValues(), 
                            $join->isGroupJoin(),
                            $join->getOuterKeyFunctionExpressionTree(),
                            $join->getInnerKeyFunctionExpressionTree(),
                            $this->provider->getFunctionToExpressionTreeConverter()->convert($applyFunction))));
        } else {
            $this->provider->execute(new Queries\OperationQuery(
                    $this->scope, 
                    new Queries\Operations\JoinApply(
                            $join->getValues(), 
                            $join->isGroupJoin(),
                            $join->getOnFunctionExpressionTree(),
                            $this->provider->getFunctionToExpressionTreeConverter()->convert($applyFunction))));
        }
    }
}
