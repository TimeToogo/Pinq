<?php

namespace Pinq\Connectors;

use Pinq\Interfaces;
use Pinq\Queries;
use Pinq\Queries\Segments;
use Pinq\Providers;
use Pinq\Parsing\IFunctionToExpressionTreeConverter;
use Pinq\Queries\Common\Join;

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
     * @var IFunctionToExpressionTreeConverter
     */
    protected $functionConverter;

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
     * @var Join\IFilter|null
     */
    protected $filter;
    
    /**
     * @var boolean
     */
    protected $hasDefault = false;
    
    /**
     * @var mixed
     */
    protected $defaultValue;
    
    /**
     * @var mixed
     */
    protected $defaultKey;

    /**
     * @param boolean $isGroupJoin
     */
    public function __construct(Providers\IQueryProvider $provider, Queries\IScope $scope, $innerValues, $isGroupJoin)
    {
        $this->provider = $provider;
        $this->functionConverter = $provider->getFunctionToExpressionTreeConverter();
        $this->scope = $scope;
        $this->innerValues = $innerValues;
        $this->isGroupJoin = $isGroupJoin;
    }

    public function on(callable $joiningOnFunction)
    {
        $this->filter = new Join\Filter\On($this->functionConverter->convert($joiningOnFunction));
        
        return $this;
    }

    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        $this->filter = new Join\Filter\Equality(
                $this->functionConverter->convert($outerKeyFunction), 
                $this->functionConverter->convert($innerKeyFunction));
        
        return $this;
    }
    
    public function withDefault($value, $key = null)
    {
        $this->hasDefault = true;
        $this->defaultValue = $value;
        $this->defaultKey = $key;
        
        return $this;
    }
    
    public function to(callable $joinFunction)
    {
        return $this->provider->createQueryable($this->scope->append(
                new Segments\Join(
                        $this->innerValues, 
                        $this->isGroupJoin, 
                        $this->filter,
                        $this->functionConverter->convert($joinFunction),
                        $this->hasDefault,
                        $this->defaultValue,
                        $this->defaultKey)));
    }

}
