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
class JoiningQueryable implements Interfaces\IJoiningOnQueryable
{
    /**
     * @var Providers\IQueryProvider
     */
    protected $provider;

    /**
     * @var Queries\IScope
     */
    protected $scope;

    /**
     * @var array|\Traversable
     */
    protected $innerValues;

    /**
     * @var boolean
     */
    protected $isGroupJoin;

    /**
     * @var callable|null
     */
    protected $joinSegmentFactory;

    /**
     * @param boolean $isGroupJoin
     */
    public function __construct(Providers\IQueryProvider $provider, Queries\IScope $scope, $innerValues, $isGroupJoin)
    {
        $this->provider = $provider;
        $this->scope = $scope;
        $this->innerValues = $innerValues;
        $this->isGroupJoin = $isGroupJoin;
        $this->joinSegmentFactory = function (callable $joiningFunction)  {
            return $this->createJoinSegment(
                    null, 
                    $joiningFunction);
        };
    }

    public function on(callable $joiningOnFunction)
    {
        $this->joinSegmentFactory = function (callable $joiningFunction) use ($joiningOnFunction) {
            return $this->createJoinSegment(
                    $joiningOnFunction, 
                    $joiningFunction);
        };
        
        return $this;
    }

    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        $this->joinSegmentFactory = function (callable $joiningFunction) use ($outerKeyFunction, $innerKeyFunction) {
            return $this->createEqualityJoinSegment(
                    $outerKeyFunction, 
                    $innerKeyFunction, 
                    $joiningFunction);
        };
        
        return $this;
    }
    
    public function to(callable $joinFunction)
    {
        $segmentFactory = $this->joinSegmentFactory;
        
        return $this->provider->createQueryable($this->scope->append($segmentFactory($joinFunction)));
    }
    
    final protected function createJoinSegment(callable $joiningOnFunction = null, callable $joiningToFunction)
    {
        $functionConverter = $this->provider->getFunctionToExpressionTreeConverter();
        
        return new Segments\Join(
            $this->innerValues,
            $this->isGroupJoin,
            $joiningOnFunction === null ? null : $functionConverter->convert($joiningOnFunction),
            $functionConverter->convert($joiningToFunction));
    }
    
    final protected function createEqualityJoinSegment(callable $outerKeyFunction, callable $innerKeyFunction, callable $joiningToFunction)
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
