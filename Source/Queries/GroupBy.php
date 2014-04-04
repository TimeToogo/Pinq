<?php

namespace Pinq\Queries;

use \Pinq\FunctionExpressionTree;

class GroupBy extends Query
{
    /**
     * @var FunctionExpressionTree[]
     */
    private $FunctionExpressionTrees;

    public function __construct(array $FunctionExpressionTrees)
    {
        $this->FunctionExpressionTrees = $FunctionExpressionTrees;
    }

    public function GetType()
    {
        return self::GroupBy;
    }
    
    public function GetFunctionExpressionTrees()
    {
        return $this->FunctionExpressionTrees;
    }
    
    public function TraverseQuery(QueryStreamWalker $Walker)
    {
        return $Walker->VisitGroupBy($this);
    }
    
    public function AndBy(FunctionExpressionTree $FunctionExpressionTree) 
    {
        return new self(array_merge($this->FunctionExpressionTrees, [$FunctionExpressionTree]));
    }
    
    public function Update(array $FunctionExpressionTrees)
    {
        if($this->FunctionExpressionTrees === $FunctionExpressionTrees) {
            return $this;
        }
        
        return new self($FunctionExpressionTrees);
    }
}
