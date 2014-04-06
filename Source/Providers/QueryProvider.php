<?php

namespace Pinq\Providers;

use \Pinq\Queries;
use \Pinq\Queries\Segments;
use \Pinq\Parsing\IFunctionToExpressionTreeConverter;

abstract class QueryProvider implements IQueryProvider
{
    
    /**
     * @var IFunctionToExpressionTreeConverter 
     */
    private $FunctionConverter;
    
    /**
     * @var \SplObjectStorage 
     */
    private $RequestEvaluatorCache;
    
    public function __construct(IFunctionToExpressionTreeConverter $FunctionConverter = null)
    {
        $this->FunctionConverter = $FunctionConverter ?: 
                new \Pinq\Parsing\FunctionToExpressionTreeConverter(new \Pinq\Parsing\PHPParser\Parser());
        $this->RequestEvaluatorCache = new \SplObjectStorage();
    }
    
    public function GetFunctionToExpressionTreeConverter()
    {
        return $this->FunctionConverter;
    }

    public function CreateQueryable(Queries\IScope $Scope)
    {
        if($Scope->IsEmpty()) {
            return new \Pinq\Queryable($this);
        }
        $Segments = $Scope->GetSegments();
        $LastSegment = end($Segments);
        
        if($LastSegment instanceof Segments\OrderBy) {
            return new \Pinq\OrderedQueryable($this, $Scope);
        }
        else if($LastSegment instanceof Segments\GroupBy) {
            return new \Pinq\GroupedQueryable($this, $Scope);
        }
        else {
            return new \Pinq\Queryable($this, $Scope);
        }
    }
    
    final public function Load(Queries\IRequestQuery $Query)
    {
        $Scope = $Query->GetScope();
        if(!isset($this->RequestEvaluatorCache[$Scope])) {
            $this->RequestEvaluatorCache[$Scope] = $this->LoadRequestEvaluatorVisitor($Scope);
        }
        
        $RequestEvaluator = $this->RequestEvaluatorCache[$Scope];
        return $RequestEvaluator->Visit($Query->GetRequest());
    }
    /**
     * @return Queries\Requests\RequestVisitor
     */
    protected abstract function LoadRequestEvaluatorVisitor(Queries\IScope $Scope);
}
