<?php

namespace Pinq\Providers\Loadable;

use \Pinq\Queries;
use \Pinq\Providers\Traversable;

abstract class QueryScope extends \Pinq\Providers\QueryScope
{
    /**
     * @var Traversable\QueryScope
     */
    private $LoadedQueryScope;
    
    /**
     * @var boolean
     */
    private $IsLoaded = false;
    
    public function __construct(Queries\IQueryStream $QueryStream)
    {
        parent::__construct($QueryStream);
    }
    
    final public function GetValues()
    {
        if(!$this->IsLoaded) {
            $Traversable = new \Pinq\Traversable($this->LoadValues());
            $this->LoadedQueryScope = new Traversable\QueryScope($Traversable, $this->QueryStream);
            $this->IsLoaded = true;
        }
        
        return $this->LoadedQueryScope->GetValues();
    }
    protected abstract function LoadValues();
    
    final public function First()
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->First() : $this->LoadFirst();
    }
    protected abstract function LoadFirst();
    
    final public function Count()
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Count() : $this->LoadCount();
    }
    protected abstract function LoadCount();
    
    final public function Exists()
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Exists() : $this->LoadExists();
    }
    protected abstract function LoadExists();
    
    final public function Contains($Value)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Contains($Value) : $this->LoadContains($Value);
    }
    protected abstract function LoadContains($Value);
    
    final public function Aggregate(FunctionExpressionTree $ExpressionTree)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Aggregate($ExpressionTree) : $this->LoadAggregate($ExpressionTree);
    }
    protected abstract function LoadAggregate(FunctionExpressionTree $ExpressionTree);
    
    final public function Maximum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Maximum($ExpressionTree) : $this->LoadMaximum($ExpressionTree);
    }
    protected abstract function LoadMaximum(FunctionExpressionTree $ExpressionTree = null);
    
    final public function Minimum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Minimum($ExpressionTree) : $this->LoadMinimum($ExpressionTree);
    }
    protected abstract function LoadMinimum(FunctionExpressionTree $ExpressionTree = null);
    
    final public function Sum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Sum($ExpressionTree) : $this->LoadSum($ExpressionTree);
    }
    protected abstract function LoadSum(FunctionExpressionTree $ExpressionTree = null);
    
    final public function Average(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Average($ExpressionTree) : $this->LoadAverage($ExpressionTree);
    }
    protected abstract function LoadAverage(FunctionExpressionTree $ExpressionTree = null);
    
    final public function All(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->All($ExpressionTree) : $this->LoadAll($ExpressionTree);
    }
    protected abstract function LoadAll(FunctionExpressionTree $ExpressionTree = null);
    
    final public function Any(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Any($ExpressionTree) : $this->LoadAny($ExpressionTree);
    }
    protected abstract function LoadAny(FunctionExpressionTree $ExpressionTree = null);
    
    final public function Implode($Delimiter, FunctionExpressionTree $ExpressionTree = null) 
    {
        return $this->IsLoaded ? $this->LoadedQueryScope->Implode($Delimiter, $ExpressionTree) : $this->LoadImplode($Delimiter, $ExpressionTree);
    }
    protected abstract function LoadImplode($Delimiter, FunctionExpressionTree $ExpressionTree = null);
}
