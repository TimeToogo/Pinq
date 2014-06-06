<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Queries;
use Pinq\Queries\Segments;
use Pinq\Providers;

/**
 * Implements the filtering API for a join / group join queryable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningOnQueryable implements Interfaces\IJoiningOnQueryable, Interfaces\IJoiningOnRepository
{
    /**
     * @var Providers\IQueryProvider
     */
    protected $provider;

    /**
     * @var Queries\IScope
     */
    private $scope;

    /**
     * @var array|\Traversable
     */
    private $innerValues;

    /**
     * @var boolean
     */
    private $isGroupJoin;

    /**
     * @param boolean $isGroupJoin
     */
    public function __construct(Providers\IQueryProvider $provider, Queries\IScope $scope, $innerValues, $isGroupJoin)
    {
        $this->provider = $provider;
        $this->scope = $scope;
        $this->innerValues = $innerValues;
        $this->isGroupJoin = $isGroupJoin;
    }
    
    private function createQueryableWithJoin(Segments\JoinBase $joinSegment)
    {
        return $this->createQueryable($this->scope->append($joinSegment));
    }
    
    protected function createQueryable(Queries\IScope $scope)
    {
        return $this->provider->createQueryable($scope);
    }

    public function on(callable $joiningOnFunction)
    {
        return new JoiningToConnector(
                function (callable $joiningFunction) use ($joiningOnFunction) {
                    return $this->createQueryableWithJoin(
                            $this->createJoinSegment(
                                    $joiningOnFunction, 
                                    $joiningFunction));
                });
    }
    
    private function createJoinSegment(callable $joiningOnFunction, callable $joiningToFunction)
    {
        $functionConverter = $this->provider->getFunctionToExpressionTreeConverter();
        
        return new Segments\Join(
            $this->innerValues,
            $this->isGroupJoin,
            $functionConverter->convert($joiningOnFunction),
            $functionConverter->convert($joiningToFunction));
    }

    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new JoiningToConnector(
                function (callable $joiningFunction) use ($outerKeyFunction, $innerKeyFunction) {
                    return $this->createQueryableWithJoin(
                            $this->createEqualityJoinSegment(
                                    $outerKeyFunction, 
                                    $innerKeyFunction, 
                                    $joiningFunction));
                });
    }
    
    private function createEqualityJoinSegment(callable $outerKeyFunction, callable $innerKeyFunction, callable $joiningToFunction)
    {
        $functionConverter = $this->provider->getFunctionToExpressionTreeConverter();
        
        return new Segments\EqualityJoin(
                $this->innerValues,
                $this->isGroupJoin,
                $functionConverter->convert($outerKeyFunction),
                $functionConverter->convert($innerKeyFunction),
                $functionConverter->convert($joiningToFunction));
    }
}
