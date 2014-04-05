<?php

namespace Pinq\Providers\Traversable;

use \Pinq\Queries;
use \Pinq\FunctionExpressionTree;

class QueryScope extends \Pinq\Providers\QueryScope
{
    /**
     * @var \Pinq\ITraversable
     */
    private $Traversable;
    
    public function __construct(\Pinq\ITraversable $Traversable, Queries\IQueryStream $QueryStream)
    {
        parent::__construct($QueryStream);
        $this->Traversable = $Traversable;
    }
    
    public function GetValues()
    {
        return $this->Traversable->getIterator();
    }
    
    public function First()
    {
        return $this->Traversable->First();
    }
    
    public function Last()
    {
        return $this->Traversable->Last();
    }
    
    public function Count()
    {
        return $this->Traversable->Count();
    }
    
    public function Exists()
    {
        return $this->Traversable->Exists();
    }
    
    public function Contains($Value)
    {
        return $this->Traversable->Contains($Value);
    }
    
    public function Aggregate(FunctionExpressionTree $ExpressionTree)
    {
        return $this->Traversable->Aggregate($ExpressionTree);
    }
    
    public function Maximum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->Traversable->Maximum($ExpressionTree);
    }
    
    public function Minimum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->Traversable->Minimum($ExpressionTree);
    }
    
    public function Sum(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->Traversable->Sum($ExpressionTree);
    }
    
    public function Average(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->Traversable->Average($ExpressionTree);
    }
    
    public function All(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->Traversable->All($ExpressionTree);
    }
    
    public function Any(FunctionExpressionTree $ExpressionTree = null)
    {
        return $this->Traversable->Any($ExpressionTree);
    }
    
    public function Implode($Delimiter, FunctionExpressionTree $ExpressionTree = null) 
    {
        return $this->Traversable->Implode($Delimiter, $ExpressionTree);
    }
}
