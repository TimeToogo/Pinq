<?php

namespace Pinq;

use \Pinq\Queries;
use \Pinq\Queries\Segments;

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
    private $Provider;
    
    /**
     * @var Queries\IScope
     */
    private $Scope;
    
    /**
     * @var array|\Traversable
     */
    private $InnerValues;
    
    /**
     * @var boolean
     */
    private $IsGroupJoin;
    
    /**
     * @param boolean $IsGroupJoin
     */
    public function __construct(
            Providers\IQueryProvider $Provider, 
            Queries\IScope $Scope,
            $InnerValues, 
            $IsGroupJoin)
    {
        $this->Provider = $Provider;
        $this->Scope = $Scope;
        $this->InnerValues = $InnerValues;
        $this->IsGroupJoin = $IsGroupJoin;
    }

    
    public function On(callable $JoiningOnFunction)
    {
        $FunctionConverter = $this->Provider->GetFunctionToExpressionTreeConverter();
        return new JoiningToQueryable(
                $this->Provider, 
                $this->Scope,
                function (callable $JoiningFunction) use ($FunctionConverter, $JoiningOnFunction) {
                    return new Segments\Join(
                            $this->InnerValues, 
                            $this->IsGroupJoin, 
                            $FunctionConverter->Convert($JoiningOnFunction), 
                            $FunctionConverter->Convert($JoiningFunction));
                });
    }

    public function OnEquality(callable $OuterKeyFunction, callable $InnerKeyFunction)
    {
        $FunctionConverter = $this->Provider->GetFunctionToExpressionTreeConverter();
        return new JoiningToQueryable(
                $this->Provider, 
                $this->Scope,
                function (callable $JoiningFunction) use ($FunctionConverter, $OuterKeyFunction, $InnerKeyFunction) {
                    return new Segments\EqualityJoin(
                            $this->InnerValues, 
                            $this->IsGroupJoin, 
                            $FunctionConverter->Convert($OuterKeyFunction), 
                            $FunctionConverter->Convert($InnerKeyFunction), 
                            $FunctionConverter->Convert($JoiningFunction));
                });
    }
}
