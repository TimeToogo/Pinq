<?php

namespace Pinq\Providers\Caching;

class QueryScope extends \Pinq\Providers\QueryScope
{
    /**
     * @var Traversable\QueryScope
     */
    private $InnerQueryScope;
    
    /**
     * @var array
     */
    private $Cache = [];
    
    public function __construct(\Pinq\Providers\IQueryScope $InnerQueryScope)
    {
        parent::__construct($InnerQueryScope->GetQueryStream());
        $this->InnerQueryScope = $InnerQueryScope;
    }
    
    private function CacheMethodResult($MethodName, array $Arguments = []) 
    {
        if(!isset($this->Cache[$MethodName])) {
            $this->Cache[$MethodName] = call_user_func_array([$this->InnerQueryScope, $MethodName], $Arguments);
        }
        
        return $this->Cache[$MethodName];
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
