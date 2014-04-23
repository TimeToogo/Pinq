<?php 

namespace Pinq;

use Pinq\Queries;
use Pinq\Queries\Segments;

/**
 * Implements the filtering API for a join / group join queryable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningOnQueryable implements IJoiningOnTraversable
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
    
    public function on(callable $joiningOnFunction)
    {
        $functionConverter = $this->provider->getFunctionToExpressionTreeConverter();
        
        return new JoiningToQueryable(
                $this->provider,
                $this->scope,
                function (callable $joiningFunction) use($functionConverter, $joiningOnFunction) {
            return new Segments\Join(
                            $this->innerValues,
                            $this->isGroupJoin,
                            $functionConverter->convert($joiningOnFunction),
                            $functionConverter->convert($joiningFunction));
        });
    }
    
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        $functionConverter = $this->provider->getFunctionToExpressionTreeConverter();
        
        return new JoiningToQueryable(
                $this->provider,
                $this->scope,
                function (callable $joiningFunction) use($functionConverter, $outerKeyFunction, $innerKeyFunction) {
            return new Segments\EqualityJoin(
                            $this->innerValues,
                            $this->isGroupJoin,
                            $functionConverter->convert($outerKeyFunction),
                            $functionConverter->convert($innerKeyFunction),
                            $functionConverter->convert($joiningFunction));
        });
    }
}