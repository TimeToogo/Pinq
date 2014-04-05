<?php

namespace Pinq\Providers\Caching;

use \Pinq\FunctionExpressionTree;

class QueryScope extends \Pinq\Providers\QueryScope
{
    /**
     * @var Traversable\QueryScope
     */
    private $InnerQueryScope;
    
    /**
     * @var array
     */
    private $MethodResultCache = [];
    
    /**
     * @var array
     */
    private $MethodParametersLookup = [];
    
    /**
     * @var array
     */
    private $ParameterizedMethodResultCache = [];
    
    public function __construct(\Pinq\Providers\IQueryScope $InnerQueryScope)
    {
        parent::__construct($InnerQueryScope->GetQueryStream());
        $this->InnerQueryScope = $InnerQueryScope;
    }
    
    private function CacheMethodResult($MethodName, array $Arguments = []) 
    {
        if(empty($Arguments)) {
            if(!isset($this->MethodResultCache[$MethodName])) {
                $this->MethodResultCache[$MethodName] = $this->InnerQueryScope->$MethodName();
            }

            return $this->MethodResultCache[$MethodName];
        }
        else {
            if(!isset($this->MethodParametersLookup[$MethodName])) {
                $this->MethodParametersLookup[$MethodName] = [];
            }
            $Key = array_search($Arguments, $this->MethodParametersLookup[$MethodName], true);
            
            if($Key === false) {
                $Key = count($this->MethodParametersLookup) + 1;
                $this->ParameterizedMethodResultCache[$MethodName][$Key] = call_user_func_array([$this->InnerQueryScope, $MethodName], $Arguments);
            }
            
            return $this->ParameterizedMethodResultCache[$MethodName][$Key];
        }
    }
    
    final public function GetValues()
    {
        return $this->CacheMethodResult(__FUNCTION__);
    }
    
    final public function First()
    {
        return $this->CacheMethodResult(__FUNCTION__);
    }
    
    final public function Count()
    {
        return $this->CacheMethodResult(__FUNCTION__);
    }
    
    final public function Exists()
    {
        return $this->CacheMethodResult(__FUNCTION__);
    }
    
    final public function Contains($Value)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$Value]);
    }
    
    final public function Aggregate(FunctionExpressionTree $ExpressionTree)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$ExpressionTree]);
    }
    
    final public function Maximum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$ExpressionTree]);
    }
    
    final public function Minimum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$ExpressionTree]);
    }
    
    final public function Sum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$ExpressionTree]);
    }
    
    final public function Average(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$ExpressionTree]);
    }
    
    final public function All(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$ExpressionTree]);
    }
    
    final public function Any(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->CacheMethodResult(__FUNCTION__, [$ExpressionTree]);
    }
    
    final public function Implode($Delimiter, FunctionExpressionTree $ExpressionTree = null) 
    {
        return $this->CacheMethodResult(__FUNCTION__, [$Delimiter, $ExpressionTree]);
    }
}
